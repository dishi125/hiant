import {connection as dbConnection, connection} from "./db.js";
import { Validation } from "../utils/validation.js";
import { sendTo, users } from "../server.js";
import {UPLOADS_PATH, CHAT_PAGINATION_COUNT, public_PATH, AWS_URL} from "../config/db.config.js";
import moment from "moment";
import 'moment-timezone';
import {getAllGroupChats} from "../chat/group.js";
import {response} from "express";
import path from "path";

class GroupMessage {
    constructor(message) {
        this.from_user_id = message.from_user_id;
        this.group_id = message.group_id;
        this.message = message.message;
        this.type = message.type;
        this.reply_of = message.message_id;
        // this.is_parent_read = message.is_parent_read;
        this.created_at = moment().utc().format('x');
        this.updated_at = moment().utc().format('x');
        this.kicked_user_id = message.kicked_user_id ? message.kicked_user_id : null
    }

    static addMessage(message, callback) {
        connection.query("INSERT INTO group_chat_room_messages SET ?", message, async (err, res) => {
            if (err) {
                console.log("error: ", err);
                return;
            }
            if (message["type"] == "images") {
                message["images"] = message["message"].split(',');
                message["message"] = null;
            }
            message["time"] = message["created_at"];

            connection.query("select name,avatar from users_detail where user_id = ? limit 1", [message["from_user_id"]], (err1, res1) => {
                if (err1) {
                    console.log("error: ", err1);
                    return;
                }

                message['avatar'] = public_PATH + 'img/avatar/avatar-1.png';
                message["name"] = "";
                if (res1.length > 0){
                    message["name"] = res1[0]['name'];
                    if(res1[0]['avatar']==null){
                        message['avatar'] = public_PATH + 'img/avatar/avatar-1.png';
                    }
                    else {
                        message['avatar'] = AWS_URL + res1[0]['avatar'];
                    }
                }

                connection.query(`SELECT g.message,g.type,g.created_at,g.from_user_id,u.name FROM group_chat_room_messages g INNER JOIN users_detail u ON u.user_id = g.from_user_id where g.id = ? LIMIT 1`, [message["reply_of"]], (err2, res2) => {
                    if (err2) console.log(err2);
                    if (res2.length > 0){
                        let reply_message = {};
                        reply_message.message = res2[0]["message"];
                        /*if (res2[0]["type"] == "file") {
                            reply_message.message = UPLOADS_PATH + res2[0]["message"];
                        }*/
                        reply_message.type = res2[0]["type"];
                        reply_message.name = res2[0]['name'];
                        reply_message.time = res2[0]['created_at'];
                        reply_message.user_id = res2[0]['from_user_id'];
                        message['reply_message'] = reply_message;
                    }

                    callback({ id: res.insertId, ...message });
                });

            });
        });
    }

    static getAllGroupChats(group_id, pageno, conn, search_user_id, timezone, user_id, active_members) {
        var totalData;
        var dataPerPage = CHAT_PAGINATION_COUNT;
        var offset = (pageno-1) * dataPerPage;
        var dbquery;
        if (search_user_id == 0) {
            dbquery = `SELECT SQL_CALC_FOUND_ROWS g.*,u.name, u.avatar
                           FROM group_chat_room_messages g
                                    LEFT JOIN users_detail u ON u.user_id = g.from_user_id
                           where g.group_id = ?
                           order by g.id DESC LIMIT ?,?`;
        }
        else {
            dbquery = `SELECT SQL_CALC_FOUND_ROWS g.*,u.name, u.avatar
                           FROM group_chat_room_messages g
                                    LEFT JOIN users_detail u ON u.user_id = g.from_user_id
                           where g.group_id = ? and g.from_user_id = ${search_user_id}
                           order by g.id DESC LIMIT ?,?`;
        }

        connection.query(dbquery, [group_id,offset,dataPerPage], async (err, res) =>  {
                if (err) {
                    console.log(err);
                    const validation = new Validation({
                        message: "Please contact technical team",
                        status: 500,
                        error: true
                    });
                    sendTo(
                        conn,
                        validation.convertObjectToJson(),
                        "getAllGroupChats"
                    );
                }
                else if (res.length > 0) {
                    let totalData = await new Promise((resolve, rej) => {
                        connection.query("SELECT FOUND_ROWS() as total", [], (err, res) => {
                            if (err) console.log(err);
                            resolve(res[0]?.total);
                        });
                    });
                    var total_pages = Math.ceil(totalData / dataPerPage);

                    for (var i = 0; i < res.length; i++) {
                        res[i]["time"] = res[i]["created_at"];
                    }

                    var cnt = 1;
                    var count_read_messages = [];
                    res.forEach(async (element) => {
                        if (element["type"] == "images") {
                            element["images"] = element["message"].split(',');
                            element["message"] = null;
                        }
                        else if (element["type"] == "videos") {
                            var videos_data = await new Promise((resolve, rej) => {
                                dbConnection.query("select file,video_thumbnail from group_chat_room_message_files where message_id = ? and group_id = ?", [element['id'],element['group_id']], (err, res) => {
                                    if (err) console.log(err);
                                    resolve(res);
                                });
                            });
                            var videos_thumb = [];
                            videos_data.forEach(function(obj){
                                videos_thumb.push({
                                    "video_url":obj.file,
                                    "thumbnail_url":obj.video_thumbnail
                                });
                            });
                            element["videos"] = videos_thumb;
                        }

                        if(element['avatar']==null){
                            element['avatar'] = public_PATH + 'img/avatar/avatar-1.png';
                        }
                        else {
                            element['avatar'] = AWS_URL + element['avatar'];
                        }

                        const inputFormat = 'x';
                        const inputTimezone = 'UTC';
                        const outputFormat = 'x';
                        const outputTimezone = (timezone!=undefined) ? timezone : 'Asia/Seoul'; // Target timezone
                        if (element['time']!=0){
                            const inputTime = element['time']; // Your input string time
                            const convertedTime = moment.tz(inputTime, inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                            element["time"] = parseInt(convertedTime);
                        }
                        const converted_created_at = moment.tz(element['created_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                        element["created_at"] = parseInt(converted_created_at);
                        const converted_updated_at = moment.tz(element['updated_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                        element["updated_at"] = parseInt(converted_updated_at);

                        var user_data = await new Promise((resolve, rej) => {
                            dbConnection.query("select name from users_detail where user_id = ? limit 1", [element['kicked_user_id']], (err, res) => {
                                if (err) console.log(err);
                                resolve(res);
                            });
                        });
                        if (element["type"] == "kick") {
                            element["message"] = user_data[0]['name'] + " is kicked";
                        }

                        let read_msg = await new Promise((resolve, rej) => {
                            dbConnection.query("SELECT * FROM `group_chat_room_unread_messages` WHERE `group_id` = ? and `message_id` = ? and `user_id` = ?", [group_id,element['id'],user_id], async (err, res) => {
                                if (err) console.log(err);
                                dbConnection.query("DELETE FROM group_chat_room_unread_messages WHERE `group_id` = ? and `message_id` = ? and `user_id` = ?", [group_id,element['id'],user_id], async (err, res) => {
                                    if (err) console.log(err);
                                    resolve(res);
                                })

                                if (res.length > 0){
                                    var unread_count_msg = await new Promise((resolve, rej) => {
                                        dbConnection.query("SELECT count(id) as cnt FROM group_chat_room_unread_messages where `group_id` = ? and `message_id` = ?", [group_id,element['id']], (err, res) => {
                                            if (err) console.log(err);
                                            if (res && res.length > 0) {
                                                resolve(res[0]['cnt']);
                                            } else {
                                                resolve(0);
                                            }
                                        });
                                    });

                                    count_read_messages.push({
                                        "message_id": element['id'],
                                        "unread_count": unread_count_msg
                                    });
                                }
                            });
                        });

                        let unread_count_msg = await new Promise((resolve, rej) => {
                            dbConnection.query("SELECT count(id) as cnt FROM group_chat_room_unread_messages where `group_id` = ? and `message_id` = ?", [group_id,element['id']], (err, res) => {
                                if (err) console.log(err);
                                if (res && res.length > 0) {
                                    resolve(res[0]['cnt']);
                                } else {
                                    // No matching record found in the database
                                    resolve(0);
                                }
                            });
                        });
                        element['unread_count'] = (unread_count_msg!=undefined) ? unread_count_msg : 0;

                        connection.query(`SELECT g.message,g.type,g.created_at,g.from_user_id,u.name FROM group_chat_room_messages g INNER JOIN users_detail u ON u.user_id = g.from_user_id where g.group_id = ? and g.id = ? LIMIT 1`, [group_id,element['reply_of']], (err1, res1) => {
                            if (err1) console.log(err1);
                            if (res1.length > 0){
                                let reply_message = {};
                                reply_message.message = res1[0]["message"];
                                /*if (res1[0]["type"] == "file") {
                                    reply_message.message = UPLOADS_PATH + res1[0]["message"];
                                }*/
                                reply_message.type = res1[0]["type"];
                                reply_message.name = res1[0]['name'];
                                reply_message.time = res1[0]['created_at'];
                                const converted_parent_message_time = moment.tz(reply_message["time"], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                                reply_message.time = parseInt(converted_parent_message_time);
                                reply_message.user_id = res1[0]['from_user_id'];
                                element['reply_message'] = reply_message;
                            }

                            dbConnection.query(`SELECT u.name,l.user_id FROM liked_group_chat_room_messages l INNER JOIN users_detail u ON u.user_id = l.user_id where l.message_id = ?`, [element['id']], (err2, res2) => {
                                if (err2) console.log(err2);
                                var liked_by = [];
                                res2.forEach(function(obj){
                                    liked_by.push({"name":obj.name,"user_id":obj.user_id});
                                });
                               element['liked_by'] = liked_by;

                                if (cnt == res.length){
                                    var returnData = {'total_data' : totalData, 'total_page' : total_pages,'current_page': pageno,'data_per_page': dataPerPage, 'data' : res}
                                    sendTo(
                                        conn,
                                        {
                                            type: "getAllGroupChats",
                                            data: returnData
                                        },
                                        "getAllGroupChats"
                                    );

                                    if (count_read_messages.length > 0) {
                                        active_members.forEach((element) => {
                                            if (element != user_id) {
                                                var userConnection = users[element];
                                                if (userConnection && userConnection.length > 0) {
                                                    userConnection.map((e) => {
                                                        sendTo(
                                                            e.connection,
                                                            {
                                                                type: "updateUnreadCount",
                                                                data: count_read_messages
                                                            },
                                                            "groupChat"
                                                        );
                                                    });
                                                }
                                            }
                                        })
                                    }
                                }

                                cnt++;
                            })
                        });
                    });
                }
                else {
                    console.log("No Chats");
                    const validation = new Validation({
                        message: "Don't have any chat for this group",
                        status: 200,
                        error: true,
                        value: []
                    });
                    sendTo(
                        conn,
                        validation.convertObjectToJson(),
                        "getAllGroupChats"
                    );
                }
            }
        );
    }

    static KickUser(message, callback) {
        connection.query("INSERT INTO group_chat_room_messages SET ?", message, async (err, res) => {
            if (err) {
                console.log("error: ", err);
                return;
            }

            var user_data = await new Promise((resolve, rej) => {
                dbConnection.query("select name from users_detail where user_id = ? limit 1", [message['kicked_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });

            if (message["type"] == "kick") {
                message["message"] = user_data[0]['name'] + " is kicked";
            }
            message["time"] = message["created_at"];
            message['avatar'] = null;
            message["name"] = null;

            var block_user = await new Promise((resolve, rej) => {
                dbConnection.query("INSERT INTO group_blocked_users (group_id,user_id) VALUES (?,?)", [message['group_id'],message['kicked_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });

            var unjoin_user = await new Promise((resolve, rej) => {
                dbConnection.query("DELETE FROM group_chat_room_joined_users WHERE group_chat_room_id = ? and user_id = ?", [message['group_id'],message['kicked_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });

            var delete_user_data = await new Promise((resolve, rej) => {
                dbConnection.query("DELETE FROM group_chat_room_unread_messages WHERE group_id = ? and user_id = ?", [message['group_id'],message['kicked_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });

            callback({ id: res.insertId, ...message });
        });
    }

}

export { GroupMessage };
