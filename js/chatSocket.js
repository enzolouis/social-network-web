var chatSocket;

function sendToServer(data) {
    chatSocket.send(data);
}

class Format {
    static formatSend(content) {
        return ":send: " + content;
    }

    static formatDelete(id) {
        return ":dele|" + id + ":";
    }

    static formatEdit(id, newcontent) {
        return ":edit|" + id + ": " + newcontent;
    }

    static getMessageType(content) {
        if (content.startsWith(":dele")) {
            return "dele";
        } else if (content.startsWith(":edit")) {
            return "edit";
        } else if (content.startsWith(":send")) {
            return "send";
        } else {
            return "send";
        }
    }

    static getIdFromDeleteMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(6, firstSemiColon);
    }

    static getContentFromSendMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(firstSemiColon+2, content.length);
    }

    static getIdFromEditMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        console.log("originalcontent="+content)
        console.log("id="+content.substring(6, firstSemiColon))
        console.log("firstsemicolonindex="+firstSemiColon)
        return content.substring(6, firstSemiColon);
    }

    static getContentFromEditMessage(content) {
        let firstSemiColon = content.substring(1, content.length).indexOf(":") + 1;
        return content.substring(firstSemiColon+2, content.length);
    }
}