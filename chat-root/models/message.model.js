import {connection as dbConnection, connection} from "./db.js";
import { Validation } from "../utils/validation.js";
import { sendTo, users } from "../server.js";
import {UPLOADS_PATH, CHAT_PAGINATION_COUNT, public_PATH, AWS_URL} from "../config/db.config.js";
import moment from "moment";
import 'moment-timezone';

class Message {
    constructor(message) {
        this.from_user_id = message.from_user_id;
        this.to_user_id = message.to_user_id;
        this.message = message.message;
        this.type = message.type;
        this.status = message.status;
        // this.reply_of = message.message_id;
        this.created_at = moment().utc().format('x');
        this.updated_at = moment().utc().format('x');
    }

    static addMessage(message, callback) {
        connection.query("INSERT INTO private_chat_messages SET ?", message, async (err, res) => {
            if (err) {
                console.log("error: ", err);
                return;
            }

            message["time"] = message["created_at"];

            var check_push = await new Promise((resolve, rej) => {
                dbConnection.query("SELECT * FROM `private_chat_push_notifications` WHERE `user_id` = ? and `from_user_id` = ?", [message["from_user_id"],message['to_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });
            if (check_push.length==0){
                var set_push = await new Promise((resolve, rej) => {
                    dbConnection.query("INSERT INTO private_chat_push_notifications (user_id,from_user_id,push) VALUES (?,?,?)", [message['from_user_id'],message['to_user_id'],'on'], (err, res) => {
                        if (err) console.log(err);
                        resolve(res);
                    });
                });
            }

            var check_push1 = await new Promise((resolve, rej) => {
                dbConnection.query("SELECT * FROM `private_chat_push_notifications` WHERE `user_id` = ? and `from_user_id` = ?", [message["to_user_id"],message['from_user_id']], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });
            if (check_push1.length==0){
                var set_push = await new Promise((resolve, rej) => {
                    dbConnection.query("INSERT INTO private_chat_push_notifications (user_id,from_user_id,push) VALUES (?,?,?)", [message['to_user_id'],message['from_user_id'],'on'], (err, res) => {
                        if (err) console.log(err);
                        resolve(res);
                    });
                });
            }

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

                connection.query(`SELECT p.message,p.type,p.created_at,p.from_user_id,u.name FROM private_chat_messages p INNER JOIN users_detail u ON u.user_id = p.from_user_id where p.id = ? LIMIT 1`, [message["reply_of"]], (err2, res2) => {
                    if (err2) console.log(err2);
                    if (res2.length > 0){
                        let reply_message = {};
                        reply_message.message = res2[0]["message"];
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

    static getAllChats(from,to,pageno,conn,timezone) {
        var totalData;
        var dataPerPage = CHAT_PAGINATION_COUNT;
        var offset = (pageno-1) * dataPerPage;

        connection.query(
            "SELECT SQL_CALC_FOUND_ROWS * FROM private_chat_messages where (from_user_id = ? or from_user_id = ?) and (to_user_id = ? or to_user_id = ?) order by id DESC LIMIT ?,?",
            [from, to, from, to, offset,dataPerPage],
            async (err, res) =>  {
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
                        "getAllChats"
                    );
                } else if (res.length > 0) {
                    let totalData = await new Promise((resolve, rej) => {
                        connection.query("SELECT FOUND_ROWS() as total", [], (err, res) => {
                            if (err) console.log(err);
                            resolve(res[0]?.total);
                        });
                    });
                    var total_pages = Math.ceil(totalData / dataPerPage);

                    for (var i = 0; i < res.length; i++) {
                        var date1 = new Date(res[i]["created_at"]);
                        var currentDate =
                            date1.getFullYear() +
                            "/" +
                            (date1.getMonth() + 1) +
                            "/" +
                            date1.getDate() +
                            " " +
                            date1.getHours() +
                            ":" +
                            date1.getMinutes();
                        if (i > 0) {
                            var date2 = new Date(res[i - 1]["created_at"]);
                            var previousDate =
                                date2.getFullYear() +
                                "/" +
                                (date2.getMonth() + 1) +
                                "/" +
                                date2.getDate() +
                                " " +
                                date2.getHours() +
                                ":" +
                                date2.getMinutes();
                            if (
                                currentDate == previousDate &&
                                res[i - 1]["from_user_id"] ==
                                res[i]["from_user_id"]
                            ) {
                                res[i]["time"] = 0;
                            } else {
                                res[i]["time"] = res[i]["created_at"];
                            }
                        } else {
                            res[i]["time"] = res[i]["created_at"];
                        }
                    }

                    var cnt = 1;
                    res.forEach(element => {
                        const inputFormat = 'x';
                        const inputTimezone = 'UTC';
                        const outputFormat = 'x';
                        const outputTimezone = (timezone!=undefined) ? timezone : 'Asia/Seoul'; // Target timezone
                        if (element['time']!=0){
                            const inputTime = element['time']; // Your input string time
                            // console.log("timezone "+timezone);
                            const convertedTime = moment.tz(inputTime, inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                            element["time"] = parseInt(convertedTime);
                        }
                        const converted_created_at = moment.tz(element['created_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                        element["created_at"] = parseInt(converted_created_at);
                        const converted_updated_at = moment.tz(element['updated_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                        element["updated_at"] = parseInt(converted_updated_at);

                        connection.query(`SELECT m.*,u.name FROM private_chat_messages m INNER JOIN users_detail u ON u.user_id = m.from_user_id where m.id = ? LIMIT 1`, [element['reply_of']], (err1, res1) => {
                            if (err1) console.log(err1);
                            if (res1.length > 0){
                                let reply_message = {};
                                reply_message.message = res1[0]["message"];
                                reply_message.type = res1[0]["type"];
                                reply_message.name = res1[0]['name'];
                                reply_message.time = res1[0]['created_at'];
                                const converted_parent_message_time = moment.tz(reply_message["time"], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                                reply_message.time = parseInt(converted_parent_message_time);
                                reply_message.user_id = res1[0]['from_user_id'];
                                element['reply_message'] = reply_message;
                            }

                            if (cnt == res.length){
                                var returnData = {'total_data' : totalData, 'total_page' : total_pages,'current_page': pageno,'data_per_page': dataPerPage, 'data' : res}
                                sendTo(
                                    conn,
                                    {
                                        type: "getAllChats",
                                        data: returnData
                                    },
                                    "getAllChats"
                                );
                            }

                            cnt++;
                        });
                    });

                } else {
                    console.log("No Chats");
                    const validation = new Validation({
                        message: "Don't have any chat for this users",
                        status: 200,
                        error: true,
                        value: []
                    });
                    sendTo(
                        conn,
                        validation.convertObjectToJson(),
                        "getAllChats"
                    );
                }
            }
        );
    }

    static updateReadStatus(from,to,conn) {
        var timestamp = Date.now();
        connection.query(
            "UPDATE private_chat_messages SET status = 1 WHERE from_user_id=? and to_user_id=? and created_at <= ?",
            [to, from, timestamp],
            (err, res) => {
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
                        "readMessages"
                    );
                }

                sendTo(
                    conn,
                    {
                        type: "readMessages",
                        data: (res["success"] = true)
                    },
                    "readMessages"
                );
                // console.log("from  " + to);
                if (users.hasOwnProperty(to)) {
                    var toConnection = users[to];
                    toConnection.map((e) => {
                        // console.log(e);
                        sendTo(
                            e.connection,
                            {
                                type: "readMessages",
                                data: (res["success"] = true)
                            },
                            "readMessages"
                        );
                    });
                } else {
                    // console.log("to  " + 87787);
                }
            }
        );
    }

}

export { Message };
