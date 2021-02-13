
/*
    var langState = {
        create: function(){},
        --------------------------------------- end of phaser functions
        func_setLang: function(){} //calls loadState
    };
    
    var loadState = {
        preload: function(){},
        create: function(){} //calls nameState
        -------------------------------------- end of phaser functions
    };
        
    var nameState = {
        create: function(){},
        ------------------------------------------------ end of phaser functions
        func_checkEmptyName: function(){}
        func_savename: function(){} //calls menu.js -> menuState
    };

    var buttonSettings = {
        func_addButtons: function(_,_,_,_,_,_,_,_,_){},
        loadState: function(){}
    };
*/

// "choose language" screen

var langState = {

    create: function() {

        if(this.game.world.width != defaultWidth) this.game.world.setBounds(0, 0, defaultWidth, this.game.world.height);

        // AUX

        var style = { font: '28px Arial', fill: '#00804d', align: 'center' };

        // AUDIO

        beepSound = game.add.audio('sound_beep');   // game sound
        okSound = game.add.audio('sound_ok');       // correct answer sound
        errorSound = game.add.audio('sound_error'); // wrong answer sound

        // BACKGROUND

        game.stage.backgroundColor = '#cce5ff';
        
        // LANGUAGES

        var flagObjList = [];
        var langNameList= ['FRAÇÕES  ', 'FRAZIONI  ',   'FRACTIONS  ',  'FRACCIONES  ', 'FRACTIONS  '   ];
        var flagList    = ['flag_BR',   'flag_IT',      'flag_US',      'flag_PE',      'flag_FR'       ];
        var langList    = ['pt_BR',     'it_IT',        'en_US',        'es_PE',        'fr_FR'         ];
        var x1List = [-220, -220, -220,  200, 200];
        var x2List = [-120, -120, -120,  300, 300];
        var yList  = [-180,    0,  180, -100, 100];
        
        for(var i=0; i<langList.length; i++){
            var titleList = game.add.text(this.game.world.centerX + x1List[i], this.game.world.centerY + yList[i], langNameList[i], style);
            titleList.anchor.setTo(1, 0.5);

            flagObjList[i] = game.add.sprite(this.game.world.centerX + x2List[i], this.game.world.centerY + yList[i], flagList[i]);       
            flagObjList[i].anchor.setTo(0.5, 0.5);
            flagObjList[i].inputEnabled = true;
            flagObjList[i].input.useHandCursor = true;
            flagObjList[i].events.onInputDown.add(this.func_setLang,{ lang: langList[i] });
            flagObjList[i].events.onInputOver.add(function(){ this.flagObj.scale.setTo(1.05) },{ flagObj: flagObjList[i] });
            flagObjList[i].events.onInputOut.add( function(){ this.flagObj.scale.setTo(1)    },{ flagObj: flagObjList[i] });
        }

        
    },
    
    func_setLang: function(){
        // set language
        lang = this.lang;
        // start resource loading
        game.state.start('load');
    
    }

};

// "loading" screen and load json dictionary
var loadState = {
    
    preload: function() {
        
        // Displaying the progress bar
        var progressBar = game.add.sprite(game.world.centerX, game.world.centerY, 'progressBar');
        progressBar.anchor.setTo(0.5, 0.5);
        game.load.setPreloadSprite(progressBar);
        
        // Loading dictionary
        game.load.json('dictionary', 'assets/languages/'+lang+'.json');
        
    },

    create: function() {  

        // gets selected language from json
        lang = game.cache.getJSON('dictionary');
        
        if(firstTime==true){ // select language screen - first time opening ifractions
          firstTime = false;
          game.state.start('name'); // go to select name screen, then menu
        }else{               // changing language during the game
          game.state.start('menu'); // go to menu
        }
    
    }

};

// "username" screen
var nameState = {

    create: function() {
                    
        // AUX

        var style = { font: '30px Arial', fill: '#00804d', align: 'center' };
        var styleName = { font: '44px Arial', fill: '#000000', align: 'center' };
        
        // title

        var title = game.add.text(this.game.world.centerX, this.game.world.centerY - 100, lang.insert_name, style);
        title.anchor.setTo(0.5);
        
        errorEmptyName = game.add.text(this.game.world.centerX, this.game.world.centerY - 70, "", {font: '18px Arial', fill: '#330000', align: 'center'});
        errorEmptyName.anchor.setTo(0.5);

        // "READY" button
        
        var btn = game.add.graphics(this.game.world.centerX - 84, this.game.world.centerY + 70);
        btn.lineStyle(1, 0x293d3d);
        btn.beginFill(0x3d5c5c);
        btn.drawRect(0, 0, 168, 60);
        btn.alpha = 0.5;
        btn.endFill();

        btn.inputEnabled = true;
        btn.input.useHandCursor = true;
        btn.events.onInputDown.add(this.func_checkEmptyName);
        btn.events.onInputOver.add(function(){ btn.alpha=0.4 });
        btn.events.onInputOut.add(function(){ btn.alpha=0.5 });
        
        var ready = game.add.text(this.game.world.centerX + 1, this.game.world.centerY + 102, lang.ready, { font: '34px Arial', fill: '#f0f5f5', align: 'center' });
        ready.anchor.setTo(0.5);      

        document.getElementById("text-field-div").style.visibility = "visible";
        document.getElementById("name_id").addEventListener('keypress', function(e){
            var keycode = e.keycode ? e.keycode : e.which; 
            //se apertar enter vai para ready, assim como o botão
            if(keycode == 13){
                nameState["func_checkEmptyName"]();
            }     
        });

    },
         
    func_checkEmptyName: function() {

        if(document.getElementById("name_id").value!=""){
            nameState["func_savename"]();
            errorEmptyName.setText("");
        }else{
            errorEmptyName.setText(lang.empty_name);
        }

    },
              
    func_savename: function() {
        
        // saves the typed name on username variable
        username = document.getElementById("name_id").value;
        if(debugMode) console.log("user is " + username);        

        document.getElementById("text-field-div").style.visibility = "hidden";

        //clears the text field again
        document.getElementById("name_id").value = "";

        if(audioStatus){
            beepSound.play();
        }

        game.state.start('menu');
        
    }

};

var buttonSettings = {

    m_info_left: null,

    m_back: null, 
    m_list: null,
    m_help: null,

    m_info_right: null, 

    m_audio: null,
    m_world: null, 

    xEsq: null,
    xDir: null,

    func_addButtons: function(left, right, b0Esq, b1Esq, b2Esq, b0Dir, b1Dir, phase, helpBtn){
        
        this.xDir = defaultWidth - 50 - 10;
        this.xEsq = 10;

        if(left == true){
            this.m_info_left = game.add.text(this.xEsq, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
        }

        if(right == true){
            this.m_info_right = game.add.text(this.xDir+50, 53, "", { font: "20px Arial", fill: "#330000", align: "right" });
            this.m_info_right.anchor.setTo(1,0.02);
        }

        // left buttons
        if(b0Esq == true){
            // Return to diffculty
            this.m_back = game.add.sprite(this.xEsq, 10, 'back'); 
            this.m_back.inputEnabled = true;
            this.m_back.input.useHandCursor = true;
            this.m_back.events.onInputDown.add(this.loadState, {state: phase, beep: beepSound});
            this.m_back.events.onInputOver.add(function(){ this.m_info_left.text = lang.menu_back},{m_info_left: this.m_info_left});
            this.m_back.events.onInputOut.add(function(){ this.m_info_left.text = ""},{m_info_left: this.m_info_left});
            
            this.xEsq+=50;
        }

        if(b1Esq == true){
            // Return to menu button
            this.m_list = game.add.sprite(this.xEsq, 10, 'list'); 
            this.m_list.inputEnabled = true;
            this.m_list.input.useHandCursor = true;
            this.m_list.events.onInputDown.add(this.loadState, {state: "menu", beep: beepSound});
            this.m_list.events.onInputOver.add(function(){ this.m_info_left.text = lang.menu_list},{m_info_left: this.m_info_left});
            this.m_list.events.onInputOut.add(function(){ this.m_info_left.text = ""},{m_info_left: this.m_info_left});
            
            this.xEsq+=50;
        }

        if(b2Esq == true){
            // Help button
            this.m_help = game.add.sprite(this.xEsq, 10, 'help');
            this.m_help.inputEnabled = true;
            this.m_help.input.useHandCursor = true;
            this.m_help.events.onInputDown.add(helpBtn, {beep: beepSound});
            this.m_help.events.onInputOver.add(function(){ this.m_info_left.text = lang.menu_help},{m_info_left: this.m_info_left});
            this.m_help.events.onInputOut.add(function(){ this.m_info_left.text = ""},{m_info_left: this.m_info_left});
            
            this.xEsq+=50;
        }

        // rightButtons
        if(b0Dir == true){
            this.m_audio = game.add.sprite(this.xDir, 10, 'audio');
            audioStatus ? this.m_audio.frame = 0 : this.m_audio.frame = 1;
            this.m_audio.inputEnabled = true;
            this.m_audio.input.useHandCursor = true;
            this.m_audio.events.onInputDown.add(function(){ if(audioStatus){ audioStatus=false; this.m_audio.frame = 1; }else{ audioStatus=true; this.m_audio.frame = 0; }},{m_audio: this.m_audio});
            this.m_audio.events.onInputOver.add(function(){ this.m_info_right.text = lang.audio },{m_info_right: this.m_info_right});
            this.m_audio.events.onInputOut.add(function(){ this.m_info_right.text = "" },{m_info_right: this.m_info_right});

            this.xDir-=50;
        }

        if(b1Dir == true){
            // Return to language button
            this.m_world = game.add.sprite(this.xDir, 10, 'world'); 
            this.m_world.inputEnabled = true;
            this.m_world.input.useHandCursor = true;
            this.m_world.events.onInputDown.add(this.loadState, {state: "language", beep: beepSound});
            this.m_world.events.onInputOver.add(function(){ this.m_info_right.text = lang.menu_world },{m_info_right : this.m_info_right});
            this.m_world.events.onInputOut.add(function(){ this.m_info_right.text = "" },{m_info_right: this.m_info_right});
                  
            this.xDir-=50;
        }

    },

    changeRightButtonX: function(newWidth){
        this.m_info_right.x = newWidth - 10;
        this.m_audio.x = newWidth - 50 - 10;
        console.log(this.m_audio.x+" "+newWidth);
        this.m_world.x = newWidth - 50 - 50 - 10;
    },


    loadState: function(){

        if(audioStatus){
            this.beep.play();
        }
        game.state.start(this.state);
    
    }

};