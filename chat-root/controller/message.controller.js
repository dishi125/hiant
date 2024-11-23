import { Message } from '../models/message.model.js';
import { Validation } from '../utils/validation.js';
import bcrypt from 'bcrypt';

export function getAllChats(data, connection) {
    if (data.from_user_id != null && data.to_user_id != null) {
        // console.dir(data, { depth: null });
        // console.log("timezone" + data.timezone);
        Message.getAllChats(data.from_user_id,data.to_user_id,data.page_no,connection,data.timezone)
    } else {
        const validation = new Validation({
            message: 'From User Id and To User Id is required.',
            status: 403,
            error: true
        });
        sendTo(connection, validation.convertObjectToJson(),"getAllChats");
    }
}

export function checkUnreadMessage(data, connection) {
    if (data.from_user_id != null && data.to_user_id != null) {
        Message.updateReadStatus(data.from_user_id,data.to_user_id, connection)
    } else {
        const validation = new Validation({
            message: 'From User Id and To User Id is required.',
            status: 403,
            error: true
        });
        sendTo(connection, validation.convertObjectToJson(),"readMessages");
    }
}
