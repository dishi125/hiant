import { sendTo, users, groupData } from "../server.js";
import { GroupMessage } from "../models/group-message.model.js";
import path from "path";
import fs from "fs";
import moment from "moment";
import mime from "mime";
import {connection, connection as dbConnection} from "../models/db.js";
import {admin} from '../firebase.js'
import { Validation } from '../utils/validation.js';
import {UPLOADS_PATH} from "../config/db.config.js";
import ffmpeg from 'fluent-ffmpeg';

async function groupChat(data, connection) {
    switch (data.type) {
        case "initiateGroupChat":
            console.log('initiate Group Chat');
            const groupIndex = groupData.findIndex(group => group.groupId === data.data.group_id);
            if (groupIndex !== -1) {
                if (!groupData[groupIndex].groupMembers.includes(data.data.from_user_id)) {
                    groupData[groupIndex].groupMembers.push(data.data.from_user_id);
                }
            } else {
                // console.log(`Group with groupId ${groupId} not found.`);
                groupData.push({
                    groupId: data.data.group_id,
                    groupMembers: [data.data.from_user_id]
                });
            }
            break;

        case "sendGroupMessage":
            console.log("in sendGroupMessage");
            // console.dir(data.data["message"]);
            if (data.data["type"] == "images") {
                var images = data.data["message"];
                var images_urls = [];
                images.forEach(element => {
                    // console.log(element);
                    element = "data:image/png;base64,"+element;
                    var matches = element.match(/^data:([A-Za-z-+\/]+);base64,(.+)$/);
                    var response = {};
                    if (!matches || matches.length !== 3) {
                        console.log("Invalid String");
                    }
                    response.type = matches[1];
                    response.data = new Buffer(matches[2], "base64");
                    let decodedImg = response;
                    let imageBuffer = decodedImg.data;
                    let type = decodedImg.type;
                    let extension = mime.getExtension(type);
                    let fileName = moment() + "." + extension;
                    let dir = moment().format("D-M-Y") + "/" + data.data.from_user_id;

                    var dir1 = dir.replace(/\/$/, "").split("/");
                    for (var i = 1; i <= dir1.length; i++) {
                        var segment =
                            path.basename("uploads") +
                            "/" +
                            dir1.slice(0, i).join("/");
                        !fs.existsSync(segment) ? fs.mkdirSync(segment) : null;
                    }
                    let filepath = dir + "/" + fileName;

                    try {
                        fs.writeFileSync(
                            path.basename("uploads") + "/" + filepath,
                            imageBuffer,
                            "utf8"
                        );
                        console.log("success");
                    } catch (e) {
                        //console.log(e);
                    }

                    images_urls.push(UPLOADS_PATH + path.basename("uploads") + "/" + filepath);
                });

                var msg = new GroupMessage({
                    from_user_id: data.data["from_user_id"],
                    group_id: data.data.group_id,
                    message: images_urls.join(","),
                    type: data.data["type"],
                    message_id: (data.data.message_id==0) ? null : data.data.message_id,
                    // is_parent_read: (data.data.message_id==0) ? null : 0
                });

                var sendNotificationMessage = "Images sent.";
            }
            else if (data.data["type"] == "videos") {
                var videos = data.data["message"];
                var save_videos_thumbs = await generateThumbnail(videos,data.data.from_user_id);

                var msg = new GroupMessage({
                    from_user_id: data.data["from_user_id"],
                    group_id: data.data.group_id,
                    message: null,
                    type: data.data["type"],
                    message_id: (data.data.message_id==0) ? null : data.data.message_id,
                    // is_parent_read: (data.data.message_id==0) ? null : 0
                });

                var sendNotificationMessage = "Videos sent.";
                // console.log(sendNotificationMessage);
                // console.log("videos_thumb:-");
                // console.dir(save_videos_thumbs);
            }
            else {
                var msg = new GroupMessage({
                    from_user_id: data.data["from_user_id"],
                    group_id: data.data.group_id,
                    message: data.data["message"],
                    type: data.data["type"],
                    message_id: (data.data.message_id==0) ? null : data.data.message_id,
                    // is_parent_read: (data.data.message_id==0) ? null : 0
                });

                var sendNotificationMessage = data.data["message"];
            }

            // console.log("room_mates___"+roomMates);
            GroupMessage.addMessage(msg, async function (chat) {
                const inputFormat = 'x';
                const inputTimezone = 'UTC';
                const outputFormat = 'x';
                const outputTimezone = (data.data['timezone']!=undefined) ? data.data['timezone'] : "Asia/Seoul"; // Target timezone
                if (chat['time']!=0){
                    const inputTime = chat["time"]; // Your input string time
                    const convertedTime = moment.tz(inputTime, inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                    chat["time"] = parseInt(convertedTime);
                }
                const converted_created_at = moment.tz(chat['created_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                chat["created_at"] = parseInt(converted_created_at);
                const converted_updated_at = moment.tz(chat['updated_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                chat["updated_at"] = parseInt(converted_updated_at);
                if (chat['reply_message']!=undefined){
                    const converted_parent_message_time = moment.tz(chat['reply_message']['time'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                    chat['reply_message']['time'] = parseInt(converted_parent_message_time);
                }
                if (data.data["type"] == "videos") {
                    chat['videos'] = save_videos_thumbs;
                    const save_video_data = await Promise.all(
                        save_videos_thumbs.map(async (element) => {
                            var add_video = await new Promise((resolve, rej) => {
                                dbConnection.query("INSERT INTO group_chat_room_message_files (message_id,group_id,file,video_thumbnail) VALUES (?,?,?,?)", [chat['id'],data.data.group_id, element['video_url'],element['thumbnail_url']], (err, res) => {
                                    if (err) console.log(err);
                                    resolve(res);
                                });
                            });
                            return element;
                        })
                    );
                }
                // console.dir(chat, { depth: null });

                //Notification for receive message
                let user_ids_notification = [];
                let unread_count = 0;
                let joined_users = await new Promise((resolve, rej) => {
                    dbConnection.query("SELECT `user_id` FROM `group_chat_room_joined_users` WHERE `group_chat_room_id` = ?", [data.data.group_id], (err, res) => {
                        if (err) console.log(err);
                        resolve(res);
                    });
                });
                if (joined_users.length > 0){
                    joined_users.forEach(async (element) => {
                        if (element['user_id']!=data.data.from_user_id && element['push_notification']=="on") {
                            user_ids_notification.push(element['user_id']);
                        }
                        var active_group_members = findGroupMembers(data.data.group_id);
                        if (!active_group_members.includes(element['user_id'])){
                            var add_unread_user = await new Promise((resolve, rej) => {
                                dbConnection.query("INSERT INTO group_chat_room_unread_messages (group_id,message_id,user_id) VALUES (?,?,?)", [data.data.group_id, chat['id'], element['user_id']], (err, res) => {
                                    if (err) console.log(err);
                                    unread_count = unread_count + 1;
                                    // console.log("joined_user unread_count "+unread_count);
                                    resolve(res);
                                });
                            });
                        }
                    });
                }

                let created_user = await new Promise((resolve, rej) => {
                    dbConnection.query("SELECT `created_by` FROM `group_chat_rooms` WHERE `id` = ? limit 1", [data.data.group_id], (err, res) => {
                        if (err) console.log(err);
                        resolve(res);
                    });
                });
                const collection = await Promise.all(
                    created_user.map(async (element) => {
                        if (element['created_by']!=data.data.from_user_id && element['push_notification']=="on") {
                            user_ids_notification.push(element['created_by']);
                        }

                        if (!findGroupMembers(data.data.group_id).includes(element['created_by'])){
                            var add_unread_user = await new Promise((resolve, rej) => {
                                dbConnection.query("INSERT INTO group_chat_room_unread_messages (group_id,message_id,user_id) VALUES (?,?,?)", [data.data.group_id, chat['id'], element['created_by']], (err, res) => {
                                    if (err) console.log(err);
                                    unread_count = unread_count + 1;
                                    // console.log("created_user unread_count "+unread_count);
                                    resolve(res);
                                });
                            });
                        }

                        return element;
                    })
                );

                chat['unread_count'] = unread_count;
                // console.log("chat['unread_count'] "+unread_count);
                var fromConnection = users[data.data["from_user_id"]];
                if (fromConnection && fromConnection.length > 0){
                    fromConnection.map((e) => {
                        sendTo(
                            e.connection,
                            {
                                type: "update",
                                data: chat
                            },
                            "groupChat"
                        );
                    });
                }
                joined_users.forEach(element => {
                    var toConnection = users[element['user_id']];
                    if (toConnection && toConnection.length > 0) {
                        if(element['user_id'] != data.data.from_user_id){
                            toConnection.map((e) => {
                                sendTo(
                                    e.connection,
                                    {
                                        type: "receiveMessage",
                                        data: chat
                                    },
                                    "groupChat"
                                );
                            });
                        }
                    }
                });
                created_user.forEach(element => {
                    var toConnection = users[element['created_by']];
                    if (toConnection && toConnection.length > 0) {
                        if(element['created_by'] != data.data.from_user_id){
                            toConnection.map((e) => {
                                sendTo(
                                    e.connection,
                                    {
                                        type: "receiveMessage",
                                        data: chat
                                    },
                                    "groupChat"
                                );
                            });
                        }
                    }
                });

                // console.log("user_ids_notification: "+user_ids_notification.length);
                if (user_ids_notification.length > 0) {
                    let tokenResult = await new Promise((resolve, rej) => {
                        dbConnection.query("SELECT * FROM user_devices where user_id IN (?) order by id desc", [user_ids_notification], (err, res) => {
                            if (err) console.log(err);
                            resolve(res);
                        });
                    });

                    var tokens = [];
                    if (tokenResult && tokenResult.length) {
                        Object.keys(tokenResult).forEach(function (key) {
                            var device_token = tokenResult[key].device_token;
                            tokens.push(device_token)
                        });
                    }

                    console.log("tokens.length:-" + tokens.length);
                    // console.dir(tokens);
                    if (tokens.length) {
                        var notificationMessage = {
                            notification: {
                                'title': chat['name'],
                                'body': sendNotificationMessage,
                            },
                            android: {
                                notification: {
                                    sound: 'notifytune.wav',
                                },
                                priority: 'high',
                            },
                            apns: {
                                payload: {
                                    aps: {
                                        'sound': 'notifytune.wav'
                                    }
                                }
                            },
                            data: {
                                'type': 'group_message',
                                "click_action": "FLUTTER_NOTIFICATION_CLICK",
                                "message_id": chat['id'].toString(),
                                "messageId": chat['id'].toString(),
                                "is_chat": "1"
                            },
                            tokens: tokens
                        }

                        admin.messaging().sendMulticast(notificationMessage).then((response) => {
                            // Response is a message ID string.
                            console.log('Successfully sent message');
                        }).catch((error) => {
                            console.log('Error sending message:');
                            console.log(error);
                        });
                    }
                }

            });
            break;

        case "likeMessage":
            var message_id = data.data.message_id;
            var group_id = data.data.group_id;
            dbConnection.query("SELECT * FROM `liked_group_chat_room_messages` WHERE `message_id` = ? and `user_id` = ?", [message_id,data.data.from_user_id], (err, res) => {
                if (err) console.log(err);
                if (res.length > 0) {
                    dbConnection.query("DELETE FROM liked_group_chat_room_messages WHERE message_id = ? and user_id = ?", [message_id,data.data.from_user_id],async (err, res) => {
                        if (err) console.log(err);
                    })
                } else {
                    dbConnection.query("INSERT INTO liked_group_chat_room_messages (message_id,user_id,group_id) values (?,?,?)", [message_id,data.data.from_user_id,group_id],async (err, res) => {
                        if (err) console.log(err);
                    })
                }

                dbConnection.query("SELECT `user_id` FROM `group_chat_room_joined_users` WHERE `group_chat_room_id` = ?", [group_id],async (err1, res1) => {
                    if (err1) console.log(err1);
                    // else if (res1.length > 0) {
                    dbConnection.query(`SELECT u.name,l.user_id FROM liked_group_chat_room_messages l INNER JOIN users_detail u ON u.user_id = l.user_id where l.message_id = ?`, [message_id], (err2, res2) => {
                        if (err2) console.log(err2);
                        var liked_by = [];
                        var user_ids = [];
                        res2.forEach(function(obj){
                            liked_by.push({"name":obj.name,"user_id":obj.user_id});
                            user_ids.push(obj.user_id);
                        });
                        res1.forEach(element => {
                            if (users.hasOwnProperty(element['user_id'])) {
                                var toConnection = users[element['user_id']];
                                if (toConnection && toConnection.length > 0) {
                                    toConnection.map((e) => {
                                        sendTo(
                                            e.connection,
                                            {
                                                type: "likeMessage",
                                                liked_by: liked_by,
                                                message_id: message_id,
                                                is_liked: (user_ids.includes(element['user_id']) ==true) ? 1 : 0
                                            },
                                            "groupChat"
                                        );
                                    });
                                }
                            }
                        });

                        dbConnection.query("SELECT `created_by` FROM `group_chat_rooms` WHERE `id` = ? limit 1", [group_id], (err3, res3) => {
                            if (err3) console.log(err3);

                            res3.forEach(element => {
                                if (users.hasOwnProperty(element['created_by'])) {
                                    var toConnection = users[element['created_by']];
                                    if (toConnection && toConnection.length > 0) {
                                        toConnection.map((e) => {
                                            sendTo(
                                                e.connection,
                                                {
                                                    type: "likeMessage",
                                                    liked_by: liked_by,
                                                    message_id: message_id,
                                                    is_liked: (user_ids.includes(element['created_by']) ==true) ? 1 : 0
                                                },
                                                "groupChat"
                                            );
                                        });
                                    }
                                }
                            });
                        })
                    })
                    // }
                });
            });
            break;

        case "kickUser":
            var check_already_kicked = await new Promise((resolve, rej) => {
                dbConnection.query("select * from group_blocked_users where group_id = ? and user_id = ?", [data.data.group_id,data.data["user_id"]], (err, res) => {
                    if (err) console.log(err);
                    resolve(res);
                });
            });
            if (check_already_kicked.length > 0){
                const validation = new Validation({
                    message: "User is already blocked or kicked by leader",
                    status: 401,
                    error: true
                });
                sendTo(
                    connection,
                    validation.convertObjectToJson(),
                    "groupChat"
                );
            }
            else {
                var msg = new GroupMessage({
                    from_user_id: data.data["from_user_id"],
                    group_id: data.data.group_id,
                    type: "kick",
                    kicked_user_id: data.data["user_id"],
                    message: null,
                    message_id: null
                });
                GroupMessage.KickUser(msg, async function (chat) {
                    const inputFormat = 'x';
                    const inputTimezone = 'UTC';
                    const outputFormat = 'x';
                    const outputTimezone = (data.data['timezone'] != undefined) ? data.data['timezone'] : "Asia/Seoul"; // Target timezone
                    if (chat['time'] != 0) {
                        const inputTime = chat["time"]; // Your input string time
                        const convertedTime = moment.tz(inputTime, inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                        chat["time"] = parseInt(convertedTime);
                    }
                    const converted_created_at = moment.tz(chat['created_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                    chat["created_at"] = parseInt(converted_created_at);
                    const converted_updated_at = moment.tz(chat['updated_at'], inputFormat, inputTimezone).tz(outputTimezone).format(outputFormat);
                    chat["updated_at"] = parseInt(converted_updated_at);
                    chat['unread_count'] = null;

                    var fromConnection = users[data.data["from_user_id"]];
                    if (fromConnection && fromConnection.length > 0) {
                        fromConnection.map((e) => {
                            sendTo(
                                e.connection,
                                {
                                    type: "update",
                                    data: chat
                                },
                                "groupChat"
                            );
                        });
                    }

                    var active_members = findGroupMembers(data.data.group_id);
                    // console.log("active_members: "+active_members);
                    active_members.forEach(element => {
                        // console.log("element: "+element);
                        var toConnection = users[element];
                        if (toConnection && toConnection.length > 0) {
                            if (element != data.data.from_user_id) {
                                toConnection.map((e) => {
                                    sendTo(
                                        e.connection,
                                        {
                                            type: "receiveMessage",
                                            data: chat
                                        },
                                        "groupChat"
                                    );
                                });
                            }
                        }
                    });
                });
            }
            break;
    }
}

function getAllGroupChats(data, connection){
    // if (data.from_user_id != null && data.to_user_id != null) {
    var search_user_id = (data.search_user_id==undefined) ? 0 : data.search_user_id;
    var active_members = findGroupMembers(data.group_id);
    GroupMessage.getAllGroupChats(data.group_id, data.page_no, connection, search_user_id, data.timezone, data.user_id, active_members)
    /*} else {
        const validation = new Validation({
            message: 'From User Id and To User Id is required.',
            status: 403,
            error: true
        });
        sendTo(connection, validation.convertObjectToJson(),"getAllGroupChats");
    }*/
}

// Function to find group members based on group ID
function findGroupMembers(groupId) {
    const group = groupData.find(group => group.groupId === groupId);
    return group ? group.groupMembers : [];
}

async function generateThumbnail(videos,from_user_id){
    var videos_thumb = [];
    const collection = await Promise.all(
        videos.map(async (element1) => {
            const videoBuffer = Buffer.from(element1, 'base64');
            var extension = 'mp4';
            let fileName = moment() + "." + extension;
            // console.log("video fileName: "+fileName);
            let dir = moment().format("D-M-Y") + "/" + from_user_id;
            var dir1 = dir.replace(/\/$/, "").split("/");
            for (var i = 1; i <= dir1.length; i++) {
                var segment =
                    path.basename("uploads") +
                    "/" +
                    dir1.slice(0, i).join("/");
                !fs.existsSync(segment) ? fs.mkdirSync(segment) : null;
            }
            let filepath = dir + "/" + fileName;

            try {
                fs.writeFileSync(
                    path.basename("uploads") + "/" + filepath,
                    videoBuffer,
                    "utf8"
                );
                // console.log("video saved.");
            } catch (e) {
                //console.log(e);
            }

            // const videoFileName = 'temp-video.mp4';
            const videoFileName = path.basename("uploads") + "/" + filepath;
            // fs.writeFileSync(videoFileName, videoBuffer);
            // console.log("videoFileName: "+videoFileName);
            const thumbnailFileName = moment() + ".png";
            let generate_thumbnail = await new Promise((resolve, reject) => {
                ffmpeg.ffprobe(videoFileName, (err, metadata) => {
                    // console.log("ffmpeg.ffprobe process");
                    if (err) {
                        console.error('Error getting video information:', err);
                        // Clean up by deleting the temporary video file
                        // fs.unlinkSync(videoFileName);
                        return;
                    }
                    const {width, height} = metadata.streams[0].coded_width
                        ? metadata.streams[0]
                        : metadata.streams[1]; // Handling video stream information
                    const aspectRatio = width / height;
                    // console.log("aspectRatio: "+aspectRatio);
                    // Calculate thumbnail dimensions based on aspect ratio
                    const thumbnailWidth = 320; // You can adjust this based on your needs
                    const thumbnailHeight = Math.round(thumbnailWidth / aspectRatio);
                    // Generate thumbnail using ffmpeg
                    ffmpeg(videoFileName)
                        .screenshots({
                            timestamps: ['50%'], // Capture a frame at 50% of the video's duration
                            filename: thumbnailFileName,
                            folder: path.basename("uploads") + "/" + dir + "/thumbnails/",
                            size: `${thumbnailWidth}x${thumbnailHeight}`, // Thumbnail dimensions
                        })
                        .on('end', () => {
                            // console.log('Video Thumbnail generated successfully.');
                            // fs.unlinkSync(videoFileName); // Clean up by deleting the temporary video file
                            resolve();
                        })
                        .on('error', (err) => {
                            // fs.unlinkSync(videoFileName); // Clean up by deleting the temporary video file
                            console.error('Error generating thumbnail:', err);
                            reject();
                        });
                });
            });

            videos_thumb.push({
                "video_url":UPLOADS_PATH + path.basename("uploads") + "/" + filepath,
                "thumbnail_url":UPLOADS_PATH + path.basename("uploads") + "/" + dir + "/thumbnails/" + thumbnailFileName
            });

            return element1;
        })
    );

    return videos_thumb;
}

export { groupChat,getAllGroupChats };
