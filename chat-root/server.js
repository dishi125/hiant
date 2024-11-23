import webSocket from "websocket";
import http from "http";
import { groupChat,getAllGroupChats } from "./chat/group.js";
import { chat } from "./chat/chat.js";
import { getAllChats,checkUnreadMessage } from "./controller/message.controller.js";

let WebSocketServer = webSocket.server;
let users = {};
const groupData = [];

let server = http.createServer(function(request, response) {
    // process HTTP request. Since we're writing just WebSockets
    // server we don't have to implement anything.
});
server.listen(4000, function(request, response) {
});

// create the server
let wsServer = new WebSocketServer({
    httpServer: server,
    maxReceivedFrameSize: 100000000,
    maxReceivedMessageSize: 10 * 1024 * 1024,
    autoAcceptConnections: false,
    clientConfig: {
        maxReceivedFrameSize: 100000000,
        maxReceivedMessageSize: 100000000
    }
});

// WebSocket server
wsServer.on("request", function(request) {
    let connection = request.accept(null, request.origin);
    // This is the most important callback for us, we'll handle
    // all messages from users here.
    connection.on("message", function(message) {
        if (message.type === "utf8") {
            // process WebSocket message
            manageIncomingData(message.utf8Data, connection, request);
        }
    });

    connection.on("close", function(connection) {
        // close user connection
        if (request.name && request.device_id) {
            // console.log(request);
            var found_key = -1;
            // console.log("Length: " + users[request.name].length);
            if (users.hasOwnProperty(request.name) && users[request.name].length > 0 ) {
                var requestUser = users[request.name];
                var found_key = users[request.name].findIndex(el => (el && el.device_id) == request.device_id);
                if(found_key != -1){
                    //console.log(users);
                    // console.log("found"+ found_key );
                    //delete users[request.name][found_key]
                    requestUser.splice(found_key, 1);
                    users[request.name] = requestUser;
                    /*
                    var users[request.name] = users[request.name].filter(function (el) {
                        console.log('testing el   '+el);
                      return el != null;
                  }); */
                    //  console.log(users);
                    //users[request.name].splice(found_key, 1);
                    // console.log("users[request.name].length: "+users[request.name].length);
                    if (users[request.name].length == 0){
                        removeMemberFromAllGroups(request.name);
                    }
                }
                // console.log("found"+ found_key );
            }
            console.log("socket disconnect");
            // console.dir(users);
            // console.dir(groupData);
            // delete users[request.name];
            // console.log("Length: " + users[request.name].length);
        }
    });
});

function sendTo(connection, message, type) {
    var obj = {};
    obj.type = type;
    obj.message = message;
    // console.log("obj: "+message.type);
    console.dir(obj, { depth: null });
    connection.send(JSON.stringify(obj));
}

function manageIncomingData(message, connection, request) {
    // console.log("start");
    // console.log(message);
    // console.log(connection);
    // console.log(request);
    // console.log("end");
    let data;
    //accepting only JSON messages
    try {
        data = JSON.parse(message);
        //console.log(data);
    } catch (e) {
        console.log("Invalid JSON");
        console.log("Error stack", e.stack);
        console.log("Error name", e.name);
        console.log("Error message", e.message);
        return;
    }
    switch (data.type) {
        case "addUser":
            console.log("inside adduser");
            var userDetail = JSON.parse(data.message);
            // console.log("userDetail: "+userDetail);
            users.hasOwnProperty(userDetail["from_user_id"]);
            request.name = userDetail["from_user_id"];
            request.device_id = userDetail["device_id"];
            // console.log("Device Id "+ userDetail['device_id'] );
            if (users.hasOwnProperty(userDetail["from_user_id"]) == false ) {
                users[userDetail["from_user_id"]] = [{'device_id':userDetail['device_id'],'is_admin_user':userDetail['is_admin_user'] ?? 0,'connection': connection}];
            }else {
                var is_found = false;
                users[userDetail["from_user_id"]].map((e) => {
                    if(e.device_id == userDetail['device_id']){
                        is_found = true;
                    }
                });
                if(!is_found){
                    users[userDetail["from_user_id"]].push({'device_id':userDetail['device_id'],'is_admin_user':userDetail['is_admin_user'] ?? 0,'connection': connection});
                }
            }
            // console.log("users in add-user: "+users[userDetail["from_user_id"]].connection);
            // console.dir(users);
            break;
        case "groupChat":
            groupChat(JSON.parse(data.message), connection);
            break;
        case "getAllGroupChats":
            getAllGroupChats(JSON.parse(data.message), connection);
            break;
        case "chat":
            chat(JSON.parse(data.message), connection);
            break;
        case "getAllChats":
            getAllChats(JSON.parse(data.message), connection);
            break;
        case "readMessages":
            checkUnreadMessage(JSON.parse(data.message), connection);
            break;
    }
}

// Function to remove a member from every group
function removeMemberFromAllGroups(memberToRemove) {
    groupData.forEach(group => {
        group.groupMembers = group.groupMembers.filter(member => member !== memberToRemove);
    });
}

export { users, sendTo, groupData };
