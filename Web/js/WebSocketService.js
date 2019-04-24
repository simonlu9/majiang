var WebSocketService = function(webSocket,game) {
    /*
    webSocket.onopen 		= this.onSocketOpen;
    webSocket.onclose		= this.onSocketClose;
    webSocket.onmessage 	= this.onSocketMessage;
    */
    this._socket = webSocket;
    this._game = game;
    this.onSocketOpen = function () {
        console.log(123);
        webSocket.send(JSON.stringify({type:"ready"}));
    }
    this.onSocketClose = function () {
        console.log("i am closed");
    }
    /*
    this.kaijuHandler = function (data) {
        this._game.kaiju(data.tiles,data.location);
    }*/


    /**
     * 消息分派
     * @param data
     */
    this.processMessage = function(data,game) {

        var fn = game[data.type + 'Handler'];

        if (fn) {
            game[data.type + 'Handler'](data.data);
        }
    }
    /**
     * 发送消息
     * @param msg
     */
    this.sendMessage = function(sendObj) {
          webSocket.send(JSON.stringify(sendObj));
    }

}