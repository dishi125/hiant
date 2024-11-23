import { sendTo, users } from "../server.js";
import { Message } from "../models/message.model.js";
import path from "path";
import fs from "fs";
import moment from "moment";
import 'moment-timezone';
import mime from "mime";
import {connection, connection as dbConnection} from "../models/db.js";
import {Validation} from "../utils/validation.js";
import {admin} from "../firebase.js";

let room = [];
let roomMember = [];

function chat(data, connection) {
    switch (data.type) {
        case "initiateChat":
            console.log("in private chat initiateChat");
            var myUsername = data.data.from_user_id;
            var to_user_id = data.data.to_user_id;
            var combinedUsername = myUsername + to_user_id;
            var combinedUsername2 = to_user_id + myUsername;
            var roomMates = [];
            if (
                !room.includes(combinedUsername) &&
                !room.includes(combinedUsername2)
            ) {
                room.push(combinedUsername);
                roomMember[combinedUsername] = [];
            }
            if (room.includes(combinedUsername)) {
                roomMates = roomMember[combinedUsername];
                if (!roomMates.includes(myUsername)) {
                    roomMates.push(myUsername);
                }
                // roomMember[combinedUsername] = roomMates
            } else {
                roomMates = roomMember[combinedUsername2];
                roomMates.push(myUsername);
                roomMember[combinedUsername2] = roomMates;
            }
            // console.log("room:",room);
            // console.log("roomMates:",roomMates);
            break;

        case "sendMessage":
            console.log('in private chat sendMessage');
            var myUsername = data.data.from_user_id;
            var to_user_id = data.data.to_user_id;
            var combinedUsername = myUsername + to_user_id;
            var combinedUsername2 = to_user_id + myUsername;
            var roomMates = [];
            if (room.includes(combinedUsername)) {
                roomMates = roomMember[combinedUsername];
            } else {
                roomMates = roomMember[combinedUsername2];
            }

            if (data.data["type"] == "file") {

            } else {
                var msg = new Message({
                    from_user_id: data.data["from_user_id"],
                    to_user_id: data.data["to_user_id"],
                    message: data.data["message"],
                    type: data.data["type"],
                    status: 0,
                    // message_id: (data.data.message_id==0) ? null : data.data.message_id,
                });

                var sendNotificationMessage = data.data["message"];
            }

            Message.addMessage(msg, async function(chat) {
                // console.log("Message output id" +chat['id']);
                // console.log("data.data['timezone']: "+data.data['timezone']);
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
                // console.dir(chat, { depth: null });

                var fromConnection = users[data.data["from_user_id"]];
                if (fromConnection && fromConnection.length > 0){
                    fromConnection.map((e) => {
                        sendTo(
                            e.connection,
                            {
                                type: "update",
                                data: chat
                            },
                            "chat"
                        );
                    });
                }

                var toConnection = users[data.data["to_user_id"]];
                if (toConnection && toConnection.length > 0){
                    toConnection.map((e) => {
                        sendTo(
                            e.connection,
                            {
                                type: "receiveMessage",
                                data: chat
                            },
                            "chat"
                        );
                    });
                }

                var check_push = await new Promise((resolve, rej) => {
                    dbConnection.query("SELECT * FROM `private_chat_push_notifications` WHERE `user_id` = ? and `from_user_id` = ? and `push` = 'on'", [data.data["to_user_id"],data.data["from_user_id"]], (err, res) => {
                        if (err) console.log(err);
                        resolve(res);
                    });
                });
                if (check_push.length > 0) {
                    let tokenResult = await new Promise((resolve, rej) => {
                        dbConnection.query("SELECT * FROM user_devices where user_id = ? order by id desc", [data.data["to_user_id"]], (err, res) => {
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
    }
}

export { chat };
