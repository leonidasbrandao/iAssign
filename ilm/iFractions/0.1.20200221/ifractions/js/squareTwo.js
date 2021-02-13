// Fractions Comparison Square states
/****************************** MENU ****************************/
var menuSquareTwo={
    create: function() {
        
        // Creating sound variable
        var beepSound = game.add.audio('sound_beep');
        
        // Reading dictionary
        var words = game.cache.getJSON('dictionary');
          
        // Menu options
          //information label
        m_info = game.add.text(14, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
          // Return to language button
        // Remove language icon ::Igor
        m_world = game.add.sprite(10, 10, 'about'); 
        m_world.inputEnabled = true;
        m_world.input.useHandCursor = true;
        m_world.events.onInputDown.add(showInfo);
        m_world.events.onInputOver.add(this.showOption, {message: words.menu_world});
        m_world.events.onInputOut.add(this.showOption, {message: ""});
        
          // Return to menu button
        m_list = game.add.sprite(60, 10, 'list'); 
        m_list.inputEnabled = true;
        m_list.input.useHandCursor = true;
        m_list.events.onInputDown.add(this.loadState, {state: "menu", beep: beepSound});
        m_list.events.onInputOver.add(this.showOption, {message: words.menu_list});
        m_list.events.onInputOut.add(this.showOption, {message: ""});
        
        // Setting title
        var style = { font: '28px Arial', fill: '#00804d'};
        var title = game.add.text(860, 40, words.game_menu_title, style);
        title.anchor.setTo(1, 0.5);
        
        //Showing Games and Levels
        var maxHeight = 120; //Max height of a stair
        var stairHeight = 29; //height growth of a stair
        var stairWidth = 80; //Width of a stair
        var startStair = 240;
        var startSymbol = 150;
        var startSquare = (startSymbol/2)+startStair+stairWidth*5;
        
        var bplus = game.add.sprite(startSymbol, 300, 'equal');
            bplus.frame = 0;
            bplus.scale.setTo(0.7);
            bplus.anchor.setTo(0.5,0.5);
            
         //First stairs, 6 levels

        var stairsA = [];
        for(var i=1;i<=5;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 100+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsA[i] = game.add.graphics(0, 0);
            stairsA[i].lineStyle(1, 0xFFFFFF, 1);
            stairsA[i].beginFill(0x99b3ff);
            stairsA[i].drawRect(x1, y1, x2, y2);
            stairsA[i].endFill();
            
            //event
            stairsA[i].inputEnabled = true;
            stairsA[i].input.useHandCursor = true;
            stairsA[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, type: 'A' });
            stairsA[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsA[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
            //label
            var xl = x1+stairWidth/2; //x label
            var yl = y1+(stairHeight*i)/2; //y label
            var label = game.add.text(xl, yl, i, { font: '25px Arial', fill: '#ffffff', align: 'center' });
                label.anchor.setTo(0.5, 0.4);
        }
                
        var stairsB = [];
        for(var i=1;i<=5;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 270+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsB[i] = game.add.graphics(0, 0);
            stairsB[i].lineStyle(1, 0xFFFFFF, 1);
            stairsB[i].beginFill(0xff6666);
            stairsB[i].drawRect(x1, y1, x2, y2);
            stairsB[i].endFill();
            
            //event
            stairsB[i].inputEnabled = true;
            stairsB[i].input.useHandCursor = true;
            stairsB[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, type: 'B' });
            stairsB[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsB[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
            //label
            var xl = x1+stairWidth/2; //x label
            var yl = y1+(stairHeight*i)/2; //y label
            var label = game.add.text(xl, yl, i, { font: '25px Arial', fill: '#ffffff', align: 'center' });
                label.anchor.setTo(0.5, 0.4);
        } 
        
                
        var stairsC = [];
        for(var i=1;i<=5;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 440+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsC[i] = game.add.graphics(0, 0);
            stairsC[i].lineStyle(1, 0xFFFFFF, 1);
            stairsC[i].beginFill(0xb366ff);
            stairsC[i].drawRect(x1, y1, x2, y2);
            stairsC[i].endFill();
            
            //event
            stairsC[i].inputEnabled = true;
            stairsC[i].input.useHandCursor = true;
            stairsC[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, type: 'C' });
            stairsC[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsC[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
            //label
            var xl = x1+stairWidth/2; //x label
            var yl = y1+(stairHeight*i)/2; //y label
            var label = game.add.text(xl, yl, i, { font: '25px Arial', fill: '#ffffff', align: 'center' });
                label.anchor.setTo(0.5, 0.4);
        } 

        // ::Igor
        //this.beep.play();
        twoPosition = 0; //Map position
        twoMove = true; //Move no next point
        twoDifficulty  = jogo.difficulty; //Number of difficulty (1 to 5)
        twoType = jogo.modo;
        game.state.start('mapSTwo');
        /// ::Igor

    },
    
    //Navigation functions,
    
    showOption: function(){
        m_info.text = this.message;
    },    
    
    loadState: function(){
        this.beep.play();
        game.state.start(this.state);
    },
        
        
    //MapLoading function
    loadMap: function(){
        this.beep.play();
        twoPosition = 0; //Map position
        twoMove = true; //Move no next point
        twoDifficulty  = this.difficulty; //Number of difficulty (1 to 5)
        twoType = this.type; //Operator of game
        if(twoPosition<5){
            game.state.start('mapSTwo');
        }else{
            game.state.start('unofinal');
        }
    }
    
};

/****************************** MAP ****************************/
var mapSquareTwo={
    create: function() {
                
        // Creating sound variable
        beepSound = game.add.audio('sound_beep');
        
        // Reading dictionary
        var words = game.cache.getJSON('dictionary');

        // Background
        game.add.image(0, 40, 'bgmap');
        
        if(oneMenu){
            // Menu options
              //information label
            m_info = game.add.text(14, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
              // Return to language button
            // Remove language icon ::Igor
            m_world = game.add.sprite(10, 10, 'about'); 
            m_world.inputEnabled = true;
            m_world.input.useHandCursor = true;
            m_world.events.onInputDown.add(showInfo);
            m_world.events.onInputOver.add(this.showOption, {message: words.menu_world});
            m_world.events.onInputOut.add(this.showOption, {message: ""});
              // Return to menu button
            m_list = game.add.sprite(60, 10, 'list'); 
            m_list.inputEnabled = true;
            m_list.input.useHandCursor = true;
            m_list.events.onInputDown.add(this.loadState, {state: "menu", beep: beepSound});
            m_list.events.onInputOver.add(this.showOption, {message: words.menu_list});
            m_list.events.onInputOut.add(this.showOption, {message: ""});
              // Return to diffculty
            m_back = game.add.sprite(110, 10, 'back'); 
            m_back.inputEnabled = true;
            m_back.input.useHandCursor = true;
            m_back.events.onInputDown.add(this.loadState, {state: "menuSTwo", beep: beepSound});
            m_back.events.onInputOver.add(this.showOption, {message: words.menu_back});
            m_back.events.onInputOut.add(this.showOption, {message: ""});
        }
        
        // Styles for labels
        var stylePlace = { font: '26px Arial', fill: '#ffffff', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        // Progress bar
        var percentText = onePosition*20;
        var percentBlocks = onePosition;
        for(var p=1;p<=percentBlocks;p++){
            var block = game.add.image(680+(p-1)*30, 10, 'block');
            block.scale.setTo(2, 1); //Scaling to double width
        }
        game.add.text(840, 10, percentText+'%', styleMenu);
        game.add.text(670, 10, words.difficulty + ' ' + oneDifficulty, styleMenu).anchor.setTo(1,0);
        game.add.image(680, 10, 'pgbar');
        
         //Road
        this.points = {
        'x': [ 90, 204, 318, 432, 546, 660 ],
        'y': [ 486, 422, 358, 294, 230, 166 ]
        };

        
         //Road
        this.points = {
        'x': [ 90, 204, 318, 432, 546, 660 ],
        'y': [ 486, 422, 358, 294, 230, 166 ]
        };
        
          //House
        var house = game.add.image(this.points.x[0], this.points.y[0], 'house');
        house.scale.setTo(0.7);
        house.anchor.setTo(0.7, 0.8);
         //School
        var school = game.add.image(this.points.x[5], this.points.y[5], 'school');
        school.scale.setTo(0.35);
        school.anchor.setTo(0.2, 0.7);
        
         //Trees and Rocks
        
        this.rocks = {
             'x': [156, 275, 276, 441, 452, 590, 712],
             'y': [309, 543, 259, 156, 419, 136, 316]
        }
        this.r_types = [1, 1, 2, 1, 2, 2, 2];
        
        for(var i=0; i<this.r_types.length; i++){
            if(this.r_types[i]==1){
                var sprite = game.add.image(this.rocks.x[i], this.rocks.y[i], 'rock');
                sprite.scale.setTo(0.32);
                sprite.anchor.setTo(0.5, 0.95);
            }else if(this.r_types[i]==2){
                var sprite = game.add.image(this.rocks.x[i], this.rocks.y[i], 'birch');
                sprite.scale.setTo(0.4);
                sprite.anchor.setTo(0.5, 0.95);
            }
        }
        this.trees = {
             'x': [105, 214, 354, 364, 570, 600, 740, 779],
             'y': [341, 219, 180, 520, 550, 392, 488, 286]
        }
        this.t_types = [2, 4, 3, 4, 1, 2, 4, 4];
        
        for(var i=0; i<this.t_types.length; i++){
            var sprite = game.add.image(this.trees.x[i], this.trees.y[i], 'tree'+this.t_types[i]);
            sprite.scale.setTo(0.6);
            sprite.anchor.setTo(0.5, 0.95);
        }

        
        // places
        for (var p = 1; p < this.points.x.length -1; p++){
            var place;
            if(p<twoPosition)
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_b');
            else if (twoMove && p==twoPosition)
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_b');
            else
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_a');
            place.anchor.setTo(0.5, 0.5);
            place.scale.setTo(0.3);
            var sign = game.add.image(this.points.x[p]-20, this.points.y[p]-60, 'sign');
            sign.anchor.setTo(0.5, 1);
            sign.scale.setTo(0.4);
            if(p>0 && p<this.points.x.length-1){
                var text = game.add.text(this.points.x[p]-23, this.points.y[p]-84, p, stylePlace);
                text.anchor.setTo(0.35, 0.5);
            }
        }

        // Kid start position
        this.kid = game.add.sprite(this.points.x[twoPosition], this.points.y[twoPosition], 'kid_run');
        this.kid.anchor.setTo(0.5,1);
        this.kid.scale.setTo(0.5);
        game.physics.arcade.enable(this.kid);
        this.kid.animations.add('run');
        this.kid.animations.play('run', 6, true);
        
        // Delay to next level
        this.count = 0;
        this.wait = 60;
    },

    update: function() {
        
        // Wait before moving or staring a game
        this.count ++;
        if(this.count<=this.wait) return;
        
        // If movement is stopped or position is 5 (final), load game
        if(twoPosition==5){
            twoMove = false;
        }
        
        if(!twoMove){
            this.loadGame();
        }
        
        // If momevent is enabled, move to next point from actual
        if(twoMove){
            game.physics.arcade.moveToXY(
                this.kid, 
                this.points.x[twoPosition+1],
                this.points.y[twoPosition+1],
                100
            );
            
            // I tractor reached the end, stop movement
            if(Math.ceil(this.kid.x)==this.points.x[twoPosition+1] || Math.ceil(this.kid.y)==this.points.y[twoPosition+1]){
                twoMove=false;
                twoPosition += 1; //Update position
            }
        }
    },
    //Navigation functions
    
    showOption: function(){
        m_info.text = this.message;
    },    
    
    loadState: function(){
        this.beep.play();
        game.state.start(this.state);
    },
        
    //MapLoading function
    loadGame: function(){
        beepSound.play();
        if(twoPosition<5){
            game.state.start('gameSTwo');
        }else{
            game.state.start('endSTwo');
        }
    }
};

/****************************** GAME ****************************/
var sizeA, sizeB, valueA, valueB;
var clickA, clickB, animateA, animateB, result, animate, cDelay, eDelay;
var blocksA, blocksB, auxblqA, auxblqB;
var labelA, fractionA, separatorA, labelB, fractionB, separatorB;
var kid, kidDirection, equals, counter, endCounter;
var xA, yA, xB, yB, blockW, blockH;
var okImg, errorImg;

var gameSquareTwo={
    create: function() {  
        
        // ::Igor
        if (conta == true) {
            start[iterator] = Math.floor(Date.now()/1000);
            conta = false;
        }
        
        //timer
        totalTime = 0;
        timer = game.time.create(false);
        timer.loop(1000, this.updateCounter, this);
        timer.start();        
        
        points = [2,4,6,8,9,10,12,14,15,16,18,20];
        
        // Creating sound variable
        beepSound = game.add.audio('sound_beep');
        okSound = game.add.audio('sound_ok');
        errorSound = game.add.audio('sound_error');
        
        // Reading dictionary
        var words = game.cache.getJSON('dictionary');

        // Background
        game.add.image(0, 0, 'bgimage');
        
        //Clouds
        game.add.image(300, 100, 'cloud');
        game.add.image(660, 80, 'cloud');
        game.add.image(110, 85, 'cloud').scale.setTo(0.8);
        
        // Styles for labels
        var stylePlace = { font: '26px Arial', fill: '#400080', align: 'center'};
        var styleLabel = { font: '26px Arial', fill: '#000080', align: 'center'};
        var styleFraction = { font: '20px Arial', fill: '#000080', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        //Floor
        for(var i=0;i<9;i++){
            game.add.image(i*100, 501, 'floor');
        }
             
        //kid
        kid = game.add.sprite(100, 470, 'kid_walk');
        kid.anchor.setTo(0.5, 0.7);
        kid.scale.setTo(0.8);
        kid.animations.add('right',[0,1,2,3,4,5,6,7,8,9,10,11]);
        kid.animations.add('left',[23,22,21,20,19,18,17,16,15,14,13,12]);
        kidDirection = 'right';
        kid.animations.play('right', 6, true);
                
        //Control variables
        sizeA = 0; //Size of first block
        sizeB = 0; //Size of second block
        valueA = 0; //Number of clicked blocks for a
        valueB = 0; //Number of clicked blocks for b
                
        clickA = false; //If block A is clicked
        clickB = false; //If block B is clicked
        animateA = false; //Animate A selected blocks to position
        animateB = false; //Animate B selected blocks to position
        result = false; //Game is correct
        animate = null; //Final animation sequence
        
         //generator
        console.log("Diff " + twoDifficulty + ", ini " + ((twoDifficulty-1)*2+1) + ", end " + ((twoDifficulty-1)*2+3));
        var rPoint = game.rnd.integerInRange((twoDifficulty-1)*2+1,(twoDifficulty-1)*2+3);
        sizeA = points[rPoint];
        console.log("Rpoint " + rPoint + ", val " + sizeA);
        sizeB =  this.getRndDivisor(sizeA);
        blockB = game.rnd.integerInRange(1, sizeB);
        blockA = (sizeA/sizeB) * blockB;
        
        console.log("SA " + sizeA + ", SB " + sizeB + ", BA " + blockA + ", BB " + blockB );
        
        //Blocks and fractions group
        blocksA = game.add.group(); //Main blocks A
        blocksB = game.add.group(); //Main blocks B
        auxblqA = game.add.group(); //Auxiliar blocks A
        auxblqB = game.add.group(); //Auxiliar blocks B
        
         //Creating blocks
        blockW = 400;
        blockH = 50;
        if(twoType!="C"){
            xA=230, yA=90;
            xB=xA, yB=yA+3*blockH+30;
        }else{
            xB=230, yB=90;
            xA=xB, yA=yB+3*blockH+30;
        }
             
        //Blocks A
        var widthA = blockW/sizeA;
        var lineColor = 0x1e2f2f;
        var fillColor = 0x83afaf;
        var fillColorS = 0xe0ebeb;
        
        for(var i=0; i<sizeA; i++){
            //console.log("Block A"+i+": x:"+(xA+i*widthA)+", y:"+yA);
                        
            var block = game.add.graphics(xA+i*widthA, yA);
                block.anchor.setTo(0.5, 0.5);
                block.lineStyle(2, lineColor);
                block.beginFill(fillColor);
                block.drawRect(0, 0, widthA, blockH);
                block.alpha = 0.5;
                block.endFill();

                block.inputEnabled = true;
                block.input.useHandCursor = true;
                block.events.onInputDown.add(this.clickSquare, {who: 'A',indice: i});
                block.events.onInputOver.add(this.overSquare, {who: 'A',indice: i});
                block.events.onInputOut.add(this.outSquare, {who: 'A',indice: i});
            
            blocksA.add(block);
            
            //aux blocks
            var xAux = xA+i*widthA, yAux = yA+blockH+10;
            if(twoType == 'C') yAux = yA;
            var block = game.add.graphics(xAux, yAux );
                block.anchor.setTo(0.5, 0.5);
                block.lineStyle(1, lineColor);
                block.beginFill(fillColorS);
                block.drawRect(0, 0, widthA, blockH);
                
                if(twoType!='A') block.alpha = 0;
                else block.alpha = 0.2;
                    
            auxblqA.add(block);
            
        }
        
        //label block A
        var labelX = xA+blockW+30;
        var labelY = yA+blockH/2;
        labelA = game.add.text(labelX, labelY, sizeA , styleFraction);
        labelA.anchor.setTo(0.5, 0.41);
        
        //label fraction
        labelX = xA+(blockA*widthA)+40;
        labelY = yA+blockH+34;
        fractionA = game.add.text(labelX, labelY, "0\n"+sizeA , styleFraction);
        fractionA.anchor.setTo(0.5, 0.41);
        separatorA = game.add.sprite(labelX, labelY, 'separator');
        separatorA.anchor.setTo(0.5, 0.5);
        
        fractionA.alpha = 0;
        separatorA.alpha = 0;
        
        //Blocks B
        var widthB = blockW/sizeB;
        lineColor = 0x260d0d;
        fillColor = 0xd27979;
        fillColorS = 0xf2d9d9;
               
        for(var i=0; i<sizeB; i++){
                        
            var block = game.add.graphics(xB+i*widthB, yB);
                block.anchor.setTo(0.5, 0.5);
                block.lineStyle(2, lineColor);
                block.beginFill(fillColor);
                block.drawRect(0, 0, widthB, blockH);
                block.endFill();
            
                block.inputEnabled = true;
                block.input.useHandCursor = true;
                block.events.onInputDown.add(this.clickSquare, {who: 'B',indice: i});
                block.events.onInputOver.add(this.overSquare, {who: 'B',indice: i});
                block.events.onInputOut.add(this.outSquare, {who: 'B',indice: i});

            blocksB.add(block);
            //aux blocks
            var xAux = xB+i*widthB, yAux = yB+blockH+10;
            if(twoType == 'C') yAux = yB;
            var block = game.add.graphics(xAux, yAux);
                block.anchor.setTo(0.5, 0.5);
                block.lineStyle(1, lineColor);
                block.beginFill(fillColorS);
                block.drawRect(0, 0, widthB, blockH);
                
                if(twoType!='A') block.alpha = 0;
                else block.alpha = 0.2;
            auxblqB.add(block);
            
        }
        
        //label block B
        labelX = xA+blockW+30;
        labelY = yB+blockH/2;
        labelB = game.add.text(labelX, labelY, sizeB , styleFraction);
        labelB.anchor.setTo(0.5, 0.41);    
                
        //label fraction
        labelX = xA+(blockB*widthB)+40;
        labelY = yB+blockH+34;
        fractionB = game.add.text(labelX, labelY, "0\n"+sizeB , styleFraction);
        fractionB.anchor.setTo(0.5, 0.41);
        separatorB = game.add.sprite(labelX, labelY, 'separator');
        separatorB.anchor.setTo(0.5, 0.5);
        
        fractionB.alpha = 0;
        separatorB.alpha = 0;
        
        
          //information label
        m_info = game.add.text(14, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
        
        if(twoMenu){
              // Return to language button
            // Remove language icon ::Igor
            m_world = game.add.sprite(10, 10, 'about'); 
            m_world.inputEnabled = true;
            m_world.input.useHandCursor = true;
            m_world.events.onInputDown.add(showInfo);
            m_world.events.onInputOver.add(this.showOption, {message: words.menu_world});
            m_world.events.onInputOut.add(this.showOption, {message: ""});
              // Return to menu button
            m_list = game.add.sprite(60, 10, 'list'); 
            m_list.inputEnabled = true;
            m_list.input.useHandCursor = true;
            m_list.events.onInputDown.add(this.loadState, {state: "menu", beep: beepSound});
            m_list.events.onInputOver.add(this.showOption, {message: words.menu_list});
            m_list.events.onInputOut.add(this.showOption, {message: ""});
              // Return to diffculty
            m_back = game.add.sprite(110, 10, 'back'); 
            m_back.inputEnabled = true;
            m_back.input.useHandCursor = true;
            m_back.events.onInputDown.add(this.loadState, {state: "menuSTwo", beep: beepSound});
            m_back.events.onInputOver.add(this.showOption, {message: words.menu_back});
            m_back.events.onInputOut.add(this.showOption, {message: ""});
        }
        
        //ok and error images
        okImg = game.add.image(game.world.centerX, game.world.centerY, 'h_ok');
        okImg.anchor.setTo(0.5);
        okImg.alpha = 0;
        errorImg = game.add.image(game.world.centerX, game.world.centerY, 'h_error');
        errorImg.anchor.setTo(0.5);
        errorImg.alpha = 0;
        
        counter = 0;
        endCounter = 100;
        cDelay = 0;
        eDelay = 60;
    },
    
    updateCounter: function() {
        totalTime++;
    },
    
    overSquare: function(){
        if(!clickA && this.who=="A"){
            for(var i=0;i<sizeA;i++){
                if(i<=this.indice){
                    blocksA.children[i].alpha = 1;
                }else{
                    blocksA.children[i].alpha = 0.5;
                }
            }
            fractionA.x = xA+((this.indice +1)*(blockW/sizeA))+40;
            fractionA.alpha = 1;
            fractionA.setText(this.indice +1);
        }
        if(!clickB && this.who=="B"){
            for(var i=0;i<sizeB;i++){
                if(i<=this.indice){
                    blocksB.children[i].alpha = 1;
                }else{
                    blocksB.children[i].alpha = 0.5;
                }
            }
            fractionB.x = xB+((this.indice +1)*(blockW/sizeB))+40;
            fractionB.alpha = 1;
            fractionB.setText(this.indice +1);
        }
    },
    outSquare: function(){
        if(!clickA && this.who=="A"){
            for(var i=0;i<=this.indice;i++){
                blocksA.children[i].alpha = 0.5;
            }
            fractionA.alpha = 0;
        }
        if(!clickB && this.who=="B"){
            for(var i=0;i<=this.indice;i++){
                blocksB.children[i].alpha = 0.5;
            }
            fractionB.alpha = 0;
        }
    },
    
    clickSquare: function(){
        if(!clickA && this.who=="A"){
            for(var i=0;i<sizeA;i++){
                blocksA.children[i].inputEnabled = false;
                if(i<=this.indice){
                    blocksA.children[i].alpha = 1;
                }else{
                    blocksA.children[i].alpha = 0.5;
                    auxblqA.children[i].alpha = 0;
                }
            }
            labelA.alpha = 0;
            beepSound.play();
            clickA = true;
            valueA = this.indice+1;
            fractionA.x = xA+(valueA*(blockW/sizeA))+40;
            separatorA.x = fractionA.x
            animateA = true;
        }
        if(!clickB && this.who=="B"){
            for(var i=0;i<sizeB;i++){
                blocksB.children[i].inputEnabled = false;
                if(i<=this.indice){
                    blocksB.children[i].alpha = 1;
                }else{
                    blocksB.children[i].alpha = 0.5;
                    auxblqB.children[i].alpha = 0;
                }
            }
            labelB.alpha = 0;
            beepSound.play();
            clickB = true;
            valueB = this.indice+1;
            fractionB.x = xB+(valueB*(blockW/sizeB))+40;
            separatorB.x = fractionB.x
            animateB = true;
        }
    },
    
    postScore: function (){
        
        // ::Igor
        var fi = 0;
        if (result == true) { // Correct student's result:
            hits[twoPosition - 1] ++;
            end[twoPosition - 1] = Math.floor(Date.now()/1000);
            conta = true;
            if (twoPosition == 4) {
                fi = 1;
            }
        } else { // Error student's result:
            errors[twoPosition - 1] ++;
        }
        iterator = twoPosition;
        sendResults(fi);
        
        /*var abst = "numBlocksA:"+sizeA+", valueA: " + valueA +", numBlocksB: " + sizeB + ", valueB: " + valueB;
        
        var hr = new XMLHttpRequest();
        // Create some variables we need to send to our PHP file
        var url = "resource/cn/save.php";
        var vars = "s_ip="+hip+"&s_name="+name+"&s_lang="+lang+"&s_game="+twoShape+"&s_mode="+twoType;
        vars += "&s_oper=Equal&s_leve="+twoDifficulty+"&s_posi="+twoPosition+"&s_resu="+result+"&s_time="+totalTime+"&s_deta="+abst;
        
        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            console.log(hr);

            if(hr.readyState == 4 && hr.status == 200) {
                var return_data = hr.responseText;
                console.log(return_data);
            }
        }
        // Send the data to PHP now... and wait for response to update the status div
        hr.send(vars); // Actually execute the request
        console.log("processing...");*/
    },

    update: function() {
        
        if(!(clickA && clickB) && !animate){
            if(kidDirection=='right'){
                kid.x += 1;
                if(kid.x>=800){
                    kidDirection='left';
                    kid.animations.play('left', 8, true);
                }
            }else{
                kid.x -= 1;
                if(kid.x<=100){
                    kidDirection='right';
                    kid.animations.play('right', 8, true);
                }
            }
        }

        
        if(animateA){ //If clicked A only, animate
            for(var i=0;i<valueA;i++){
                blocksA.children[i].y +=2;
            }
            if(blocksA.children[0].y>=auxblqA.children[0].y){
                animateA = false;
                fractionA.alpha = 1;
                fractionA.setText(valueA+"\n"+sizeA);
                separatorA.alpha = 1;
            }
        }
        if(animateB){ //If clicked B only, animate
            for(var i=0;i<valueB;i++){
                blocksB.children[i].y +=2;
            }
            if(blocksB.children[0].y>=auxblqB.children[0].y){
                animateB = false;
                fractionB.alpha = 1;
                fractionB.setText(valueB+"\n"+sizeB);
                separatorB.alpha = 1;
            }
        }
        
        if(clickA && clickB && !this.animate){
            //Check result
            timer.stop();
            cDelay++;
            if(cDelay>=eDelay){
                if((valueA/sizeA) == (valueB/sizeB)){
                    result = true;
                    twoMove = true;
                    okSound.play();
                    okImg.alpha = 1;
                }else{
                    result = false;
                    twoMove = false;
                    errorSound.play();
                    kid.animations.stop();
                    errorImg.alpha = 1;
                }
                this.postScore();
                clickA = false;
                clickB = false;
                animate = true;
            }
        }
        
        if(animate){
            counter++;
            if(result){
                kid.x += 2;
                kidDirection='right';
                kid.animations.play('right', 8, true);
            }
            if(counter>endCounter){
                game.state.start('mapSTwo');
            }
        }
    },
  
    //Navigation functions,
    
    showOption: function(){
        m_info.text = this.message;
    },    
    
    loadState: function(){
        this.beep.play();
        game.state.start(this.state);
    },
    
    //MapLoading function
    loadGame: function(){
        beepSound.play();
        if(onePosition<5){
            game.state.start('gameSOne');
        }else{
            game.state.start('endSOne');
        }
    },
    //Calculation help functions
    getRndDivisor: function(number){ //Get random divisor for a number
        var div = []; //Divisors found
        var p = 0; //current dividor index
        for(var i=2; i<number;i++){
            if(number%i==0){
                div[p] = i;
                p++;
            }
        }
        var x = game.rnd.integerInRange(0,p-1);
        return div[x];
    }
    
};
/****************************** END ****************************/
var endSquareTwo={
    create: function() {  
        
        // Creating sound variable
        beepSound = game.add.audio('sound_beep');
        okSound = game.add.audio('sound_ok');
        errorSound = game.add.audio('sound_error');
        
        // Reading dictionary
        var words = game.cache.getJSON('dictionary');

        // Background
        game.add.image(0, 0, 'bgimage');
                
        //Clouds
        game.add.image(300, 100, 'cloud');
        game.add.image(660, 80, 'cloud');
        game.add.image(110, 85, 'cloud').scale.setTo(0.8);
        
        // Styles for labels
        var stylePlace = { font: '26px Arial', fill: '#400080', align: 'center'};
        var styleLabel = { font: '26px Arial', fill: '#000080', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        //Floor
        for(var i=0;i<9;i++){
            game.add.image(i*100, 501, 'floor');
        }
        
        // Progress bar
        for(var p=1;p<=5;p++){
            var block = game.add.image(672+(p-1)*30, 10, 'block');
            block.scale.setTo(2, 1); //Scaling to double width
        }
        game.add.text(820, 10, '100%', styleMenu);
        game.add.text(660, 10, words.difficulty + ' ' + oneDifficulty, styleMenu).anchor.setTo(1,0);
        game.add.image(670, 10, 'pgbar');
        
        //School and trees
        game.add.sprite(600, 222 , 'school').scale.setTo(0.7);
        game.add.sprite(30, 280 , 'tree4');
        game.add.sprite(360, 250 , 'tree2');
        
        //kid
        this.kid = game.add.sprite(0, 460 , 'kid_run');
        this.kid.anchor.setTo(0.5,0.5);
        this.kid.scale.setTo(0.7);
        this.kid.animations.add('walk', [0,1,2,3,4,5,6,7,8,9,10,11]);
        this.kid.animations.play('walk', 6, true);
    },

    update: function() {
        if(this.kid.x<=700){
            this.kid.x += 2;
        }else{
            if(twoMenu){
                // REDIRECIONAR AQUI!! ::Igor
                if (redir == true) {
                    this.kid.animations.stop();
                    finish_redirect();
                    redir = false;
                }
            }else{
                this.kid.animations.stop();
            }
        }
    },
    
    verPrincipal: function(){
        game.state.start('welcome');
    },
    
    verMenu: function(){
        if(twoMenu){
            game.state.start('menu');
        }
    }               
};
