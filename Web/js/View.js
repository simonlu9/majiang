(function () {
    Majiang.View = {
        imgHtml: function(p) {
            return p ? '<img class="pai" data-pai="' + p
                + '" src="images/' + p + '.gif">'
                : '<img class="pai" src="images/pai.gif">';
        },
        zimoHtml:function (p) {
            return p ? '<img class="zimo" data-pai="' + p
                + '" src="images/' + p + '.gif">'
                : '<img class="zimo" src="images/pai.gif">';
        }
    };
    Majiang.View.init = function(game){
        //初始化事件
        $("#bottom").delegate("img","click",function(){
            if(game.me.ifPlay==false){
                alert('不能出牌');
                return;
            }
            //暗杠的时候可以选择性操作
            $('#play-notice span').css('display','none');
            game.me.setIfPlay(false);
            game.playEvent($(this).attr('data-pai'));
        })
        $("#play-notice .peng").click(function (e) {
            if(game.me.ifPeng==false){
                alert('不能碰牌');
                return;
            }
            game.me.setIfPeng(false);
            game.pengEvent($(this).attr('data-pai'));
        });
        $("#play-notice .gang").click(function (e) {
            if(game.me.ifGang==false){
                alert('不能杠牌');
                return;
            }
            game.me.setIfGang(false);
            game.gangEvent($(this).attr('data-pai'));
        })
        $("#play-notice .buGang").click(function (e) {
            if(game.me.ifBuGang==false){
                alert('不能补杠');
                return;
            }
            game.me.setIfBuGang(false);
            game.buGangEvent($(this).attr('data-pai'),$(this).attr('data-from-location'));
        });
        $("#play-notice .hu").click(function (e) {
            if(game.me.ifHu==false){
                alert('不能胡');
                return;
            }
            game.me.setIfHu(false);
            game.huEvent();
            $('#play-notice span').css('display','none');
        })
        //这里要判断当前出牌的是不是自己
        $("#play-notice .pass").click(function (e) {
            if(game.me.ifPass==false){
                alert('不能过');
                return;
            }
            if(game.me.ifPeng){
                game.me.setIfPeng(false);
                game.passEvent();
            }else if(game.me.ifBuGang && game.me.location != $(this).attr('data-from-location')){//补杠来自他人game.passEvent();
                game.me.setIfPeng(false);
                game.me.setIfBuGang(false);
                game.passEvent();
            }else if(game.me.ifHu){
                game.me.setIfHu(false);
            }else if(game.me.ifGang){
                game.me.setIfGang(false);
            }

            $('#play-notice span').css('display','none');
        })

    }

    Majiang.View.play = function(player,data){ //出牌
          var playAreaSelector = $('#play-area '+player.playAreaSelector);
         playAreaSelector.append(Majiang.View.imgHtml(data.tile));
        var tileContainer = $(player.tilesSelector+' .float-left');
      /*
        if(tileContainer.find('.zimo').length==1){
            tileContainer.find('.zimo').remove();
        } */
        tileContainer.empty();
        var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<tiles.length;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));
        if(data.notice.peng){

            $('#play-notice .peng').css('display','inline-block').attr('data-pai',data.tile);
            $('#play-notice .pass').css('display','inline-block');
        }
        if(data.notice.gang){
            $('#play-notice .gang').css('display','inline-block').attr('data-pai',data.tile);
            $('#play-notice .pass').css('display','inline-block');
        }
        if(data.notice.buGang){
            $('#play-notice .buGang').css('display','inline-block').attr('data-pai',data.tile).attr('data-from-location',player.location);
            $('#play-notice .pass').css('display','inline-block').attr('data-from-location',player.location);
        }


    }
    Majiang.View.peng = function(player,tile,fromPlayer){
        var tileContainer = $(player.tilesSelector+' .float-left');
        var pengContainer = $(player.tilesSelector+' .float-right');
        tileContainer.empty();
        var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<tiles.length;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));
        tilesHtml = [];
        for (var j=0;j<3;j++){
            tilesHtml.push( Majiang.View.imgHtml(tile));
        }
        pengContainer.prepend(tilesHtml.join(""));
        //去除上一个tile
        var playAreaSelector = $('#play-area '+fromPlayer.playAreaSelector);
        playAreaSelector.find('img:last').remove();
        //隐藏碰按钮
        $('#play-notice span').hide();


    }
    /**
     * 暗杠消息
     * @param player
     * @param tile
     */
    Majiang.View.gang = function(player,tile){
        var tileContainer = $(player.tilesSelector+' .float-left');
        var gangContainer = $(player.tilesSelector+' .float-right');
        tileContainer.empty();
        var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<tiles.length;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));
        tilesHtml = [];
        for (var j=0;j<4;j++){
            tilesHtml.push( Majiang.View.imgHtml(tile));
        }
        gangContainer.prepend(tilesHtml.join(""));
        //隐藏碰和杠按钮
        $('#play-notice span').hide();

    }
    /**
     *补杠
     * @param player
     * @param tile
     */
    Majiang.View.buGang = function(player,tile,fromPlayer){
        var tileContainer = $(player.tilesSelector+' .float-left');
        var gangContainer = $(player.tilesSelector+' .float-right');
        tileContainer.empty();
        var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<tiles.length;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));
        if(player.location!=fromPlayer.location){ //说明自己有三只
            tilesHtml = [];
            for (var j=0;j<4;j++){
                tilesHtml.push( Majiang.View.imgHtml(tile));
            }
            gangContainer.prepend(tilesHtml.join(""));

        }else{ //自己摸的
            //去除上一个tile
            var playAreaSelector = $('#play-area '+fromPlayer.playAreaSelector);
            playAreaSelector.find('img:last').remove();
            var gangHtml = Majiang.View.imgHtml(tile);
            $(gangHtml).insertAfter(gangContainer.find("[data-pai='"+(tile)+"']:last"));
        }
        //隐藏碰和杠按钮
        $('#play-notice span').hide();
    }

    Majiang.View.jieju = function(player){
        var tileContainer = $(player.tilesSelector+' .float-left');
        tileContainer.empty();
        var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<tiles.length;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));

    }

        Majiang.View.zimo = function(player,data){
      //  var tilesSelecor = ['#bottom','#right','#top','#left'];
        var tileContainer = $(player.tilesSelector+' .float-left');
        tileContainer.append(Majiang.View.zimoHtml(player.tiles[player.tiles.length-1]));
        $('#center .curr').removeClass('curr');
        $('#center').find("[data-position='"+(player.location)+"']").addClass('curr');
        //显示提示
        if(data.notice.gang){
            $('#play-notice .gang').css('display','inline-block').attr('data-pai',data.tile);
            $('#play-notice .pass').css('display','inline-block');
        }
        if(data.notice.buGang){
            $('#play-notice .buGang').css('display','inline-block').attr('data-pai',data.tile).attr('data-from-location',player.location);
            $('#play-notice .pass').css('display','inline-block').attr('data-from-location',player.location);
        }
        if(data.notice.hu){
                $('#play-notice .hu').css('display','inline-block');
                $('#play-notice .pass').css('display','inline-block');
        }
        console.log(data.tilesCount);

    }
    Majiang.View.initLocation = function(location){
        var locationText = ['东','南','西','北'];
        var locationSelector = ['#east','#south','#west','#north'];

        for(var i =0;i<4;i++){
            $(locationSelector[i]).text(locationText[(location+i)%4]).attr('data-position',(location+i)%4);
        }
    }
    Majiang.View.playOrder = function(order){
        $('#center .curr').removeClass('curr');
        $('#center').find("[data-position='"+(order)+"']").addClass('curr');
    }
    Majiang.View.initShouPai = function (player) {
         //整理手牌

        var tileContainer = $(player.tilesSelector+' .float-left');
       var tiles = player.tiles.sort();
        var tilesHtml = [];
        for (var i=0;i<13;i++){
            tilesHtml.push( Majiang.View.imgHtml(tiles[i]));
        }
        tileContainer.append(tilesHtml.join(""));


    }
})();