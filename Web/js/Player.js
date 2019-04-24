(function(){
Majiang.Player = function (tiles,location,tilesSelector,playAreaSelector) {
    this.tiles = tiles;
    this.location = location;
    this.tilesSelector = tilesSelector;
    this.playAreaSelector = playAreaSelector;
    this.ifPlay = false;
    this.ifPeng = false;
    this.ifGang = false;
    this.ifBuGang = false;
    this.ifPass = false;
    this.ifHu = false;
    this.pengTiles = [];
    this.gangTiles = [];
}

Majiang.Player.prototype.action = function(type, data, callback) {
    if("peng" == type){
        this.peng(data);
    }else if("gang" == type){
        this.gang(data);
    }else if("dapai" == type){

    }else if("kaiju" == type){

    }else if("ready"==type){ //准备中

    }

}


Majiang.Player.prototype.peng = function (tile) {
    this.pengTiles.push(tile);
    var index;
    for (var i=0;i<2;i++){
        index = this.tiles.indexOf(tile);
        if(index>-1){
            this.tiles.splice(index, 1);
        }else{
            this.tiles.pop();
        }
    }
}
    /**
     * 明杠
     * @param tile
     */
    Majiang.Player.prototype.gang = function(tile){
        this.gangTiles.push(tile);
        for (var i=0;i<4;i++){
            index = this.tiles.indexOf(tile);
            if(index>-1){
                this.tiles.splice(index, 1);
            }else{
                this.tiles.pop();
            }
        }
}

Majiang.Player.prototype.buGang = function(tile,fromPlayer){
        if(this.location==fromPlayer.location){
            for (var i=0;i<1;i++){
              var  index = this.tiles.indexOf(tile);
                if(index>-1){
                    this.tiles.splice(index, 1);
                }else{
                    this.tiles.pop();
                }
            }
        }else{
            for (var i=0;i<4;i++){
               var  index = this.tiles.indexOf(tile);
                if(index>-1){
                    this.tiles.splice(index, 1);
                }else{
                    this.tiles.pop();
                }
            }
        }

    }
    Majiang.Player.prototype.jieju = function(tiles){
        this.tiles = tiles;
    }


    Majiang.Player.prototype.setIfPass = function(val){
        this.ifPass = val;
    }
    Majiang.Player.prototype.setIfPlay = function(val){
        this.ifPlay = val;
    }
    Majiang.Player.prototype.setIfPeng = function(val){
            this.ifPeng = val;
    }
    Majiang.Player.prototype.setIfGang = function(val){
            this.ifGang = val;
    }
    Majiang.Player.prototype.setIfBuGang = function(val){
            this.ifBuGang = val;
    }
    Majiang.Player.prototype.setIfHu = function (val) {
        this.ifHu = val;
    }




})();