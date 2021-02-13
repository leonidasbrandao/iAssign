var bootState = {
    preload: function () {
        //Progress bar image
        game.load.image('progressBar', 'resource/img/global/pgbar.png');
        //loading flags (manually)
        game.load.image('flag_BR', 'resource/img/flag/BRAZ.jpg');
        game.load.image('flag_PE', 'resource/img/flag/PERU.jpg');
        game.load.image('flag_US', 'resource/img/flag/UNST.jpg');
        game.load.image('flag_FR', 'resource/img/flag/FRAN.jpg');
    },
    create: function() {
        //game settings
        game.stage.backgroundColor = '#cce5ff';
        
        game.physics.startSystem(Phaser.Physics.ARCADE);
        var style = { font: '28px Arial', fill: '#00804d', align: 'center' };
        
        /*
        //generating language selection
         //pt_BR
        var title1 = game.add.text(this.game.world.centerX - 220, this.game.world.centerY - 100, 'FRACÇÕES', style);
        title1.anchor.setTo(1, 0.5);
        var flag1 = game.add.sprite(this.game.world.centerX - 220, this.game.world.centerY - 100, 'flag_BR');       
        flag1.anchor.setTo(0, 0.5);
        flag1.inputEnabled = true;
        flag1.input.useHandCursor = true;
        flag1.events.onInputDown.add(this.setLang,{lang:'pt_BR'});
         //es_PE
        var title2 = game.add.text(this.game.world.centerX + 200, this.game.world.centerY - 100, 'FRACCIONES', style);
        title2.anchor.setTo(1, 0.5);
        var flag2 = game.add.sprite(this.game.world.centerX + 200, this.game.world.centerY - 100, 'flag_PE');       
        flag2.anchor.setTo(0, 0.5);
        flag2.inputEnabled = true;
        flag2.input.useHandCursor = true;
        flag2.events.onInputDown.add(this.setLang,{lang:'es_PE'});
         //en_US
        var title3 = game.add.text(this.game.world.centerX - 220, this.game.world.centerY + 100, 'FRACTIONS', style);
        title3.anchor.setTo(1, 0.5);
        var flag3 = game.add.sprite(this.game.world.centerX - 220, this.game.world.centerY + 100, 'flag_US');       
        flag3.anchor.setTo(0, 0.5);
        flag3.inputEnabled = true;
        flag3.input.useHandCursor = true;
        flag3.events.onInputDown.add(this.setLang,{lang:'en_US'});
         //fr_FR
        var title4 = game.add.text(this.game.world.centerX + 200, this.game.world.centerY + 100, 'FRACTIONS', style);
        title4.anchor.setTo(1, 0.5);
        var flag4 = game.add.sprite(this.game.world.centerX + 200, this.game.world.centerY + 100, 'flag_FR');       
        flag4.anchor.setTo(0, 0.5);
        flag4.inputEnabled = true;
        flag4.input.useHandCursor = true;
        flag4.events.onInputDown.add(this.setLang,{lang:'fr_FR'});
        */
        
        // Esquiva do menu de idioma ::Igor
        game.state.start('load');
    },
    
    setLang: function(){
        //set language
        lang = this.lang;
        //start resource loading
        game.state.start('load');
    }
};

var loadState = {
    
    preload: function () {
        //Progress bar image
        game.load.image('progressBar', 'resource/img/global/pgbar.png');
    },
    
    preload: function () {
        
        // Displaying the progress bar
        var progressBar = game.add.sprite(game.world.centerX, game.world.centerY, 'progressBar');
        progressBar.anchor.setTo(0.5, 0.5);
        game.load.setPreloadSprite(progressBar);
        
        if(!oneMenu){
            game.stage.backgroundColor = '#cce5ff';
            game.physics.startSystem(Phaser.Physics.ARCADE);
        }
        
        // Loading dictionary
        game.load.json('dictionary', 'resource/lang/'+lang+'.json');
                
        // Loading global assets (sprites and images)
        var baseimg = 'resource/img/global/';
        
        game.load.image('bgimage', baseimg+'bg.jpg');
        game.load.image('bgmap', baseimg+'bg_map.png');
        game.load.image('cloud', baseimg+'cloud.png');
        game.load.image('floor', baseimg+'floor.png');
        game.load.image('road', baseimg+'road.png');
        
         //games list buttons
        game.load.image('game1c', baseimg+'game/one-c.png');
		game.load.image('game2c', baseimg+'game/two-c.png');
		game.load.image('game3c', baseimg+'game/three-c.png');
		game.load.image('game4c', baseimg+'game/four-c.png');
        game.load.image('game1s', baseimg+'game/one-s.png');
		game.load.image('game2s', baseimg+'game/two-s.png');
		game.load.image('game3s', baseimg+'game/three-s.png');
		game.load.image('game4s', baseimg+'game/four-s.png');
		game.load.image('game5s', baseimg+'game/five-s.png');
         //header menu
        game.load.image('back', baseimg+'menu/back.png');
        game.load.image('home', baseimg+'menu/home.png');
        game.load.image('info', baseimg+'menu/info.png');
        game.load.image('about', baseimg+'menu/info.png');
        game.load.image('list', baseimg+'menu/menu.png');
        game.load.image('help', baseimg+'menu/help.png');
        game.load.image('pgbar', baseimg+'menu/progressBar.png');
        game.load.image('block', baseimg+'menu/block.png');
        game.load.image('eraser', baseimg+'menu/eraser.png');
         //operators
		game.load.image('add', baseimg+'operator/add.png');
		game.load.image('subtract', baseimg+'operator/subtract.png');
		game.load.image('separator', baseimg+'operator/separator.png');
		game.load.image('equal', baseimg+'operator/equal.png');
         //helpers
		game.load.image('h_arrow', baseimg+'help/arrow.png');
		game.load.image('h_double', baseimg+'help/double.png');
		game.load.image('h_error', baseimg+'help/error.png');
		game.load.image('h_ok', baseimg+'help/ok.png');
        game.load.image('down', baseimg+'help/down.png');        
        game.load.image('pointer', baseimg+'help/pointer.png');
        
        // Loading assets based on lang
        var basesrc = 'resource/img/'+lang+'/';
        
        game.load.spritesheet('kid_run', basesrc+'kid/run.png', 82, 178, 12);
        game.load.spritesheet('kid_walk', basesrc+'kid/walk.png', 78, 175, 24);
        game.load.spritesheet('kid_lost', basesrc+'kid/lost.png', 72, 170, 6);
        game.load.spritesheet('tractor', basesrc+'tractor/frame.png', 201, 144, 10);
        game.load.image('balloon', basesrc+'airballoon_upper.png');
        game.load.image('balloon_basket', basesrc+'airballoon_base.png');
        game.load.image('birch', basesrc+'birch.png');
        game.load.image('flag', basesrc+'flag.png');
        game.load.image('house', basesrc+'house.png');
        game.load.image('place_a', basesrc+'place_a.png');
        game.load.image('place_b', basesrc+'place_b.png');
        game.load.image('garage', basesrc+'garage.png');
        game.load.image('farm', basesrc+'farm.png');
        game.load.image('rock', basesrc+'rock.png');
        game.load.image('school', basesrc+'school.png');
        game.load.image('sign', basesrc+'sign.png');
        game.load.image('tree1', basesrc+'tree.png');
        game.load.image('tree2', basesrc+'tree2.png');
        game.load.image('tree3', basesrc+'tree3.png');
        game.load.image('tree4', basesrc+'tree4.png');
        
                
        // Loadind Sound Effects
        game.load.audio('sound_ok', ['resource/fx/ok.ogg', 'resource/fx/ok.mp3']);
        game.load.audio('sound_error', ['resource/fx/error.ogg', 'resource/fx/error.mp3']);
        game.load.audio('sound_beep', ['resource/fx/beep.ogg', 'resource/fx/beep.mp3']);
        
    },
    create: function() {  
        game.state.start('name');
    }
};

var nameLbl;
var nameState = {
    preload: function () {
        //textbox
    },
    create: function() {
        //game settings
        game.stage.backgroundColor = '#cce5ff';
        
        var words =  game.cache.getJSON('dictionary');
        
        game.physics.startSystem(Phaser.Physics.ARCADE);
        var style = { font: '30px Arial', fill: '#00804d', align: 'center' };
        var styleName = { font: '44px Arial', fill: '#000000', align: 'center' };
        
        //generating language selection
        var titulo = game.add.text(this.game.world.centerX, this.game.world.centerY - 100, words.insert_name, style);
        titulo.anchor.setTo(0.5);
        
        var lineColor = 0x000000;
        var fillColor = 0xfffff0;
        
        var nameBox = game.add.graphics(this.game.world.centerX - 200, this.game.world.centerY - 50);
        nameBox.lineStyle(2, lineColor);
        nameBox.beginFill(fillColor);
        nameBox.drawRect(0, 0, 400, 100);
        nameBox.alpha = 0.5;
        nameBox.endFill();
        
        nameLbl = game.add.text(this.game.world.centerX, this.game.world.centerY, "", styleName);
        nameLbl.anchor.setTo(0.5);
        
        var clear = game.add.sprite(game.world.centerX + 250, game.world.centerY, 'eraser');
        clear.scale.setTo(0.8);
        clear.anchor.setTo(0.5);
        
        clear.inputEnabled = true;
        clear.input.useHandCursor = true;
        clear.events.onInputDown.add(this.clearName, null);
        
        var btn = game.add.graphics(this.game.world.centerX - 84, this.game.world.centerY +70);
        btn.lineStyle(1, 0x293d3d);
        btn.beginFill(0x3d5c5c);
        btn.drawRect(0, 0, 168, 60);
        btn.alpha = 0.5;
        btn.endFill();
        
        btn.inputEnabled = true;
        btn.input.useHandCursor = true;
        btn.events.onInputDown.add(this.ready, null);
        
        var listo = game.add.text(this.game.world.centerX+1, this.game.world.centerY + 102, words.ready, { font: '34px Arial', fill: '#f0f5f5', align: 'center' });
        listo.anchor.setTo(0.5);
        
        game.input.keyboard.addCallbacks(this, null, null, this.keyPress);
        
        // Chama direto o menu ::Igor
        name = "Maria";
        console.log("name is " + name);
        game.state.start('menu');
    },
    
    clearName: function(){
        nameLbl.text  = "";
    },
    
    keyPress: function(char) {
        var RegExpression = /^[a-zA-Z\s]*$/; 

        if (RegExpression.test(char)) {
            nameLbl.text += char;
        }        
    },
    ready: function(){
        //var txt = nameLbl.text.trim;
        console.log(nameLbl.text.length);
        if(nameLbl.text.trim().length==0){
            nameLbl.text  = "";
            return;
        }
        
        name = nameLbl.text.trim();
        console.log("name is " + name);
        
        
        if(oneMenu){ //If menu, normal game
            // Go to the menu state
            game.state.start('menu');
        }else{ //parameters game
            if(oneShape=="Circle"){
                game.state.start("mapCOne");
            }else if(oneShape=="Square"){
                if(oneOperator=='Mixed'){
                    twoPosition = 0;
                    twoMove = true;
                    twoDifficulty = oneDifficulty; 
                    twoOperator= "";
                    twoLabel= false;
                    twoShape = oneShape;
                    twoType = oneType;
                    twoMenu = false;
                    
                    game.state.start("mapSTwo");
                }else{
                    game.state.start("mapSOne");
                }
            }
            
        }
        
    }
};