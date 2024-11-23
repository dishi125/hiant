import { sendTo } from '../server.js';

class Validation {

    constructor(validation) {
        this.message = validation.message;
        this.status = validation.status;
        this.error = validation.error;
        this.value = validation.value;
    }

    convertObjectToJson() {
        var obj = {}
        obj.message = this.message;
        obj.status = this.status;
        obj.error = this.error;
        if (this.value != null) {
            obj.result = this.value;
        }
        return obj;
    }
    static checkIsNotEmpty(data, label, connection,type) {
        if (data == null || data.length <= 0) {
            const validation = new Validation({
                message: 'Please enter ' + label,
                status: 422,
                error: true
            });
            sendTo(connection, validation.convertObjectToJson(),type)
            return false;
        } else {
            return true;
        }
    }
}
export { Validation };