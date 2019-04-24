(function() {
    Majiang.Game = function (socketService,viewService) {
        this._viewService = viewService;
        this._viewService.init(this);
        this._socketService = socketService;
        this._players = [];
        this.me = null;
        this.roundId = null;
    }
    /**
     * 玩家出牌事件处理
     * @param tile
     */
    Majiang.Game.prototype.playEvent = function(tile){
        var player = this.me;
        var msg = new Majiang.Msg('play',{'location':player.location,'tile':tile,'roundId':this.roundId});
        this._socketService.sendMessage(msg);

    }
    /**
     * 玩家碰牌事件处理
     * @param tile
     */
    Majiang.Game.prototype.pengEvent = function(tile){
        var player = this.me;
        var msg = new Majiang.Msg('peng',{'location':player.location,'tile':tile,'roundId':this.roundId});
        this._socketService.sendMessage(msg);
    }
    /**
     * 杠事件处理
     * @param tile
     */
    Majiang.Game.prototype.gangEvent = function(tile){
        var player = this.me;
        var msg = new Majiang.Msg('gang',{'location':player.location,'tile':tile,'roundId':this.roundId});
        this._socketService.sendMessage(msg);

    }
    /**
     * 补杠事件处理
     * @param tile
     * @param fromLocation
     */
    Majiang.Game.prototype.buGangEvent = function(tile,fromLocation){
        var player = this.me;
        var msg = new Majiang.Msg('buGang',{'location':player.location,'tile':tile,'roundId':this.roundId,'fromLocation':fromLocation});
        this._socketService.sendMessage(msg);
    }
    /**
     * 食胡事件
     */
    Majiang.Game.prototype.huEvent = function(){
        var player = this.me;
        var msg = new Majiang.Msg('hu',{'location':player.location});
        this._socketService.sendMessage(msg);
    }
    /**
     * pass事件处理
     */
    Majiang.Game.prototype.passEvent = function(){
        var player = this.me;
        var msg = new Majiang.Msg('pass',{'location':player.location,'roundId':this.roundId});
        this._socketService.sendMessage(msg);
    }
    Majiang.Game.prototype.huEvent = function(){
        var player = this.me;
        var msg = new Majiang.Msg('hu',{'location':player.location,'roundId':this.roundId});
        this._socketService.sendMessage(msg);
    }

    Majiang.Game.prototype.readyHandler =function(data){
        console.log(data);
    }
    /**
     * 摸牌处理
     * @param data
     */
    Majiang.Game.prototype.zimoHandler =function(data){
        var player = this._players[data.location];
        player.tiles.push(data.tile);
        player.setIfPlay(true);
        if(data.notice.gang){
            this.me.setIfGang(true);
        }
        if(data.notice.buGang){
            this.me.setIfBuGang(true);
        }
        if(data.notice.hu){
            this.me.setIfHu(true);
        }
        this._viewService.zimo(player,data);
    }
    /**
     * 出牌处理
     * @param data
     */
    Majiang.Game.prototype.playHandler  = function(data){
        var player = this._players[data.location];
        //player.tiles.pop();
        var index = player.tiles.indexOf(data.tile);
        if (index > -1) {
            player.tiles.splice(index, 1);
        }else{
            player.tiles.pop();
        }
        if(data.notice.peng){
            this.me.setIfPeng(true);
            this.me.setIfPass(true);
        }
        if(data.notice.gang){
            this.me.setIfGang(true);
            this.me.setIfPass(true)
        }
        if(data.notice.buGang){
            this.me.setIfBuGang(true);
            this.me.setIfPass(true)
        }

        this._viewService.play(player,data);

    }
    Majiang.Game.prototype.playOrderHandler = function(data){
        var player = this._players[data.location];
        player.setIfPlay(true);
        this._viewService.playOrder(data.location);

    }
    /**
     * 碰牌处理
     * @param data
     */
    Majiang.Game.prototype.pengHandler = function(data){
        var player = this._players[data.location];
        var fromPlayer =  this._players[data.fromLocation];
        player.peng(data.tile);
        this._viewService.peng(player,data.tile,fromPlayer);
    }
    Majiang.Game.prototype.gangHandler = function(data){
        var player = this._players[data.location];
        player.gang(data.tile);
        this._viewService.gang(player,data.tile);
   }
    Majiang.Game.prototype.buGangHandler = function(data){
        var player = this._players[data.location];
        var fromPlayer =  this._players[data.fromLocation];
        player.buGang(data.tile,fromPlayer);
        this._viewService.buGang(player,data.tile,fromPlayer);
    }
    Majiang.Game.prototype.huHandler = function(data){

    }

    /**
     * 开局处理
     * @param tiles
     * @param location
     */
    Majiang.Game.prototype.kaijuHandler = function(data) {
        var locationText = ['东','南','西','北']; //1 2 3 4
        var player;
        var tiles;
        var tilesSeletor = ['#bottom','#right','#top','#left'];
        var playAreaSelector = ['.main','.xiajia','.duijia','.shangjia'];
        for(var i =0;i<4;i++){
            tiles = i==0?data.tiles: new Array(13);
            player = new Majiang.Player(tiles,(data.location+i)%4,tilesSeletor[i],playAreaSelector[i]);
            this._players[player.location] = player;
            this._viewService.initShouPai(player);
        }
        this.me =  this._players[data.location];
        this.roundId = data.roundId;
        this._viewService.initLocation( data.location);
    }
    /**
     * 结局处理（开牌）
     * @param data
     */
    Majiang.Game.prototype.jiejuHandler = function(data) {
        for(var i in data.tiles){
            this._players[i].jieju(data.tiles[i]);
            this._viewService.jieju( this._players[i]);
        }
    }



})();