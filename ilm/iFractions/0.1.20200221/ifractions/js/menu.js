var menu1, menu2, menu3, menu4;
var m_info, m_world, m_menu, m_back, m_help;
var beepSound;

var words;
var lbl_game;
var menuState={
    create: function() {
        
        // Creating sound variable
        beepSound = game.add.audio('sound_beep');
        
        // Reading dictionary
        words = game.cache.getJSON('dictionary');
        
        // Setting title
        var style = { font: "32px Arial", fill: "#00804d", align: "center" };
        var title = game.add.text(this.game.world.centerX, 80, words.menu_title, style);
        title.anchor.setTo(0.5,0.5);
        
        // game selection text
        var style_game = { font: "27px Arial", fill: "#003cb3", align: "center" };
        lbl_game = game.add.text(this.game.world.centerX, 110, "", style_game);
        lbl_game.anchor.setTo(0.5,0.5);
        
        // Menu options
         //information label
        m_info = game.add.text(14, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
          // Return to language button
        
        // Remove language icon ::Igor
        m_world = game.add.sprite(10, 10, 'about'); 
        m_world.inputEnabled = true;
        m_world.input.useHandCursor = true;
        m_world.events.onInputDown.add(this.loadState, {state: "boot", beep: beepSound});
        m_world.events.onInputOver.add(this.showOption, {message: words.menu_world});
        m_world.events.onInputOut.add(this.showOption, {message: ""});
                
        // List buttons
        menu1 = game.add.sprite(this.game.world.centerX + 10, this.game.world.centerY - 70, 'game1c');
        menu2 = game.add.sprite(this.game.world.centerX + 160, this.game.world.centerY - 70, 'game2c');
        menu3 = game.add.sprite(this.game.world.centerX + 10, this.game.world.centerY + 90, 'game3c');
        menu4 = game.add.sprite(this.game.world.centerX + 160, this.game.world.centerY + 90, 'game4c');
        
        menu5 = game.add.sprite(this.game.world.centerX - 350, this.game.world.centerY - 70, 'game1s');
        menu6 = game.add.sprite(this.game.world.centerX - 200, this.game.world.centerY - 70, 'game2s');
        menu7 = game.add.sprite(this.game.world.centerX - 350, this.game.world.centerY + 90, 'game3s');
        menu8 = game.add.sprite(this.game.world.centerX - 200, this.game.world.centerY + 90, 'game4s');
        
        menu9 = game.add.sprite(this.game.world.centerX + 350, this.game.world.centerY -70, 'game5s');
        
        // Buttons actions
        menu1.anchor.setTo(0.5, 0.5);
        menu1.inputEnabled = true;
        menu1.input.useHandCursor = true;
        menu1.events.onInputDown.add(this.loadGame,{num:1, beep: beepSound, shape : "Circle", label : true});
        menu1.events.onInputOver.add(this.showTitle,{num:1, beep: beepSound, shape : "Circle", label : true});
        menu1.events.onInputOut.add(this.clearTitle);
        
        menu2.anchor.setTo(0.5, 0.5);
        menu2.inputEnabled = true;
        menu2.input.useHandCursor = true;
        menu2.events.onInputDown.add(this.loadGame,{num:2, beep: beepSound, shape : "Circle", label : false});
        menu2.events.onInputOver.add(this.showTitle,{num:2, beep: beepSound, shape : "Circle", label : false});
        menu2.events.onInputOut.add(this.clearTitle);
        
        menu3.anchor.setTo(0.5, 0.5);
        menu3.inputEnabled = true;
        menu3.input.useHandCursor = true;
        menu3.events.onInputDown.add(this.loadGame,{num:3, beep: beepSound, shape : "Circle", label : true});
        menu3.events.onInputOver.add(this.showTitle,{num:3, beep: beepSound, shape : "Circle", label : true});
        menu3.events.onInputOut.add(this.clearTitle);
        
        menu4.anchor.setTo(0.5, 0.5);
        menu4.inputEnabled = true;
        menu4.input.useHandCursor = true;
        menu4.events.onInputDown.add(this.loadGame,{num:4, beep: beepSound, shape : "Circle", label : false});
        menu4.events.onInputOver.add(this.showTitle,{num:4, beep: beepSound, shape : "Circle", label : false});
        menu4.events.onInputOut.add(this.clearTitle);
        
        menu5.anchor.setTo(0.5, 0.5);
        menu5.inputEnabled = true;
        menu5.input.useHandCursor = true;
        menu5.events.onInputDown.add(this.loadGame,{num:1, beep: beepSound, shape : "Square", label : true});
        menu5.events.onInputOver.add(this.showTitle,{num:1, beep: beepSound, shape : "Square", label : true});
        menu5.events.onInputOut.add(this.clearTitle);
        
        menu6.anchor.setTo(0.5, 0.5);
        menu6.inputEnabled = true;
        menu6.input.useHandCursor = true;
        menu6.events.onInputDown.add(this.loadGame,{num:2, beep: beepSound, shape : "Square", label : false});
        menu6.events.onInputOver.add(this.showTitle,{num:2, beep: beepSound, shape : "Square", label : false});
        menu6.events.onInputOut.add(this.clearTitle);
        
        menu7.anchor.setTo(0.5, 0.5);
        menu7.inputEnabled = true;
        menu7.input.useHandCursor = true;
        menu7.events.onInputDown.add(this.loadGame,{num:3, beep: beepSound, shape : "Square", label : true});
        menu7.events.onInputOver.add(this.showTitle,{num:3, beep: beepSound, shape : "Square", label : true});
        menu7.events.onInputOut.add(this.clearTitle);
        
        menu8.anchor.setTo(0.5, 0.5);
        menu8.inputEnabled = true;
        menu8.input.useHandCursor = true;
        menu8.events.onInputDown.add(this.loadGame,{num:4, beep: beepSound, shape : "Square", label : false});
        menu8.events.onInputOver.add(this.showTitle,{num:4, beep: beepSound, shape : "Square", label : false});
        menu8.events.onInputOut.add(this.clearTitle);
        
        menu9.anchor.setTo(0.5, 0.5);
        menu9.inputEnabled = true;
        menu9.input.useHandCursor = true;
        menu9.events.onInputDown.add(this.loadGame,{num:5, beep: beepSound, shape : "Square", label : false});
        menu9.events.onInputOver.add(this.showTitle,{num:5, beep: beepSound, shape : "Square", label : false});
        menu9.events.onInputOut.add(this.clearTitle);

        // Floor
        for(var i=0;i<9;i++){
            game.add.image(i*100, 501, 'floor');
        }
        
        // ::Igor
        this.num = parseInt(jogo.num);
        oneShape = jogo.shape;
        this.shape= jogo.shape;
        oneLabel = jogo.label;
        oneType = jogo.modo;
        twoType = jogo.modo;
        
        if( (this.num==1 || this.num==2) && this.shape=="Circle"){
            game.state.start('menuCOne');
        }
        if( (this.num==3 || this.num==4) && this.shape=="Circle"){
            game.state.start('menuCOne');
        }
        if( (this.num==1 || this.num==2) && this.shape=="Square"){
            game.state.start('menuSOne');
        }
        if( (this.num==3 || this.num==4) && this.shape=="Square"){
            game.state.start('menuSOne');
        }
        if( this.num==5 && this.shape=="Square"){
            game.state.start('menuSTwo');
        }
        // ::Igor

        
    },
    
    loadGame: function(){
        this.beep.play();
        if( (this.num==1 || this.num==2) && this.shape=="Circle"){
            oneShape = this.shape;
            oneLabel = this.label;
            oneType = "A";
            game.state.start('menuCOne');
        }
        if( (this.num==3 || this.num==4) && this.shape=="Circle"){
            oneShape = this.shape;
            oneLabel = this.label;
            oneType = "B";
            game.state.start('menuCOne');
        }
        if( (this.num==1 || this.num==2) && this.shape=="Square"){
            oneShape = this.shape;
            oneLabel = this.label;
            oneType = "A";
            game.state.start('menuSOne');
        }
        if( (this.num==3 || this.num==4) && this.shape=="Square"){
            oneShape = this.shape;
            oneLabel = this.label;
            oneType = "B";
            game.state.start('menuSOne');
        }
        if( this.num==5 && this.shape=="Square"){
            twoShape = this.shape;
            twoLabel = this.label;
            twoType = "";
            game.state.start('menuSTwo');
        }
    },
    showTitle: function(){
        
        var title = "";
        var type = "";
        
        if( (this.num==1 || this.num==2) ){
            type = "A";
        }
        if( (this.num==3 || this.num==4) ){
            type = "B";
        }
        if( this.num==5 && this.shape=="Square"){
            type = "C";
        }
        
        if(this.shape=="Circle"){
            title += words.circle_name;
        }else if(this.shape=="Square"){
            title += words.square_name;
        }
        
        if(type!=""){
            title  += ", "+words.mode_name+ " "+type;
        }
        
        if(this.label){
            title += ", " + words.with_name + " " + words.label_name;
        }else{
            title += ", " + words.without_name + " " + words.label_name;
        }
        
        lbl_game.text = title;
    },
    clearTitle: function(){
        lbl_game.text = "";
    },
    
    showOption: function(){
        m_info.text = this.message;
    },    
    
    loadState: function(){
        this.beep.play();
        game.state.start(this.state);
    }
    
};

