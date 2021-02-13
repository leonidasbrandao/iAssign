// Tractor and Square states

/****************************** MENU ****************************/
var stairsRight, stairsLeft;
var menuSquareOne={
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
        var stairHeight = 40; //height growth of a stair
        var stairWidth = 100; //Width of a stair
        var startStair = 320;
        var startSymbol = 150;
        var startSquare = (startSymbol/2)+startStair+stairWidth*3;
        
         //First stairs, plus, 3 levels, blue square
        var blueSquare = game.add.graphics(startSquare, 175);
            blueSquare.anchor.setTo(0.5,0.5);
            blueSquare.lineStyle(2, 0x31314e);
            blueSquare.beginFill(0xefeff5);
            blueSquare.drawRect(0, 0, 80, 40);
            blueSquare.endFill();
        var bplus = game.add.sprite(startSymbol, 195, 'h_arrow');
            bplus.frame = 0;
            bplus.scale.setTo(0.7);
            bplus.anchor.setTo(0.5,0.5);
        
        var stairsPlus = [];
        for(var i=1;i<=3;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 135+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsPlus[i] = game.add.graphics(0, 0);
            stairsPlus[i].lineStyle(1, 0xFFFFFF, 1);
            stairsPlus[i].beginFill(0x99b3ff);
            stairsPlus[i].drawRect(x1, y1, x2, y2);
            stairsPlus[i].endFill();
            
            //event
            stairsPlus[i].inputEnabled = true;
            stairsPlus[i].input.useHandCursor = true;
            stairsPlus[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, operator: 'Plus' });
            stairsPlus[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsPlus[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
            //label
            var xl = x1+stairWidth/2; //x label
            var yl = y1+(stairHeight*i)/2; //y label
            var label = game.add.text(xl, yl, i, { font: '25px Arial', fill: '#ffffff', align: 'center' });
                label.anchor.setTo(0.5, 0.4);
        }
        
        //Second stairs, minus, 5 levels, red Square
        var redSquare = game.add.graphics(startSquare, 330);
            redSquare.anchor.setTo(0.5,0.5);
            redSquare.lineStyle(2, 0xb30000);
            redSquare.beginFill(0xefeff5);
            redSquare.drawRect(0, 0, 80, 40);
            redSquare.endFill();
        var rminus = game.add.sprite(startSymbol, 350, 'h_arrow');
            rminus.frame = 5;
            rminus.scale.setTo(0.7);
            rminus.scale.x *= -1;
            rminus.anchor.setTo(0.5,0.5);
        
        var stairsMinus = [];
        for(var i=1;i<=3;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 285+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsMinus[i] = game.add.graphics(0, 0);
            stairsMinus[i].lineStyle(1, 0xFFFFFF, 1);
            stairsMinus[i].beginFill(0xff6666);
            stairsMinus[i].drawRect(x1, y1, x2, y2);
            stairsMinus[i].endFill();
            
            //event
            stairsMinus[i].inputEnabled = true;
            stairsMinus[i].input.useHandCursor = true;
            stairsMinus[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, operator: 'Minus' });
            stairsMinus[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsMinus[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
            //label
            var xl = x1+stairWidth/2; //x label
            var yl = y1+(stairHeight*i)/2; //y label
            var label = game.add.text(xl, yl, i, { font: '25px Arial', fill: '#ffffff', align: 'center' });
                label.anchor.setTo(0.5, 0.4);
        }
        
        // ::Igor
        //this.beep.play();
        onePosition = 0; //Map position
        oneMove = true; //Move no next point
        oneDifficulty  = jogo.difficulty; //Number of difficulty (1 to 5)
        oneOperator = jogo.operator;
        oneLabel = (jogo.label == 'true');
        game.state.start('mapSOne');
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
        onePosition = 0; //Map position
        oneMove = true; //Move no next point
        oneDifficulty  = this.difficulty; //Number of difficulty (1 to 5)
        oneOperator = this.operator; //Operator of game
        if(onePosition<5){
            game.state.start('mapSOne');
        }else{
            game.state.start('unofinal');
        }
    }
    
};

/****************************** MAP ****************************/
var mapSquareOne={
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
            m_back.events.onInputDown.add(this.loadState, {state: "menuSOne", beep: beepSound});
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

          //Garage
        var garage = game.add.image(this.points.x[0], this.points.y[0], 'garage');
        garage.scale.setTo(0.4);
        garage.anchor.setTo(0.5, 1);
         //Farm
        var farm = game.add.image(this.points.x[5], this.points.y[5], 'farm');
        farm.scale.setTo(0.6);
        farm.anchor.setTo(0.1, 0.7);
        
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
            if(p<onePosition)
                	place = game.add.image(this.points.x[p], this.points.y[p], 'place_b');
            else if (oneMove && p==onePosition)
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

        // tractor start position
        this.tractor = game.add.sprite(this.points.x[onePosition], this.points.y[onePosition], 'tractor');
        this.tractor.anchor.setTo(0.5, 1);
        this.tractor.scale.setTo(0.5);
        game.physics.arcade.enable(this.tractor);
        var walk = this.tractor.animations.add('walk',[0,1,2,3,4]);
        this.tractor.animations.play('walk', 5, true);
        this.tractor.angle -= 10;
        
        // Delay to next level
        this.count = 0;
        this.wait = 60;
        
    },

    update: function() {
        
        // Wait 2 seconds before moving or staring a game
        this.count ++;
        if(this.count<=this.wait) return;
        
        // If movement is stopped or position is 6 (final), load game
        if(onePosition==6){
            oneMove = false;
        }
        if(!oneMove){
            this.loadGame();
        }
        
        // If momevent is enabled, move to next point from actual
        if(oneMove){
            game.physics.arcade.moveToXY(
                this.tractor, 
                this.points.x[onePosition+1],
                this.points.y[onePosition+1],
                100
            );
            
            // I tractor reached the end, stop movement
            if(Math.ceil(this.tractor.x)==this.points.x[onePosition+1] || Math.ceil(this.tractor.y)==this.points.y[onePosition+1]){
                oneMove=false;
                onePosition += 1; //Update position
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
    }
};

/****************************** GAME ****************************/
var clicked, hideLabels, animate, checkCollide, result, move, moveCounter, moveEnd, hasFigure;

var startX, tractor, arrow;

var maxBlocks, blocks, numBlocks, curBlock, blockDirection, blockDistance, blockLabel, blockSeparator;

var blockWidth, endPosition, blockIndex;

var floorBlocks, floorIndex, floorCount, floorClicked;
var arrowPlace, fractionClicked, fractionIndex;

var okImg, errorImg;
var curFloor;

var detail;
var gameSquareOne={
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
        detail="";
        
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
        var styleFraction = { font: '15px Arial', fill: '#000080', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        //Floor and road
        var startX = 170; //Initial tractor and place position
        if(oneOperator=='Minus') startX = 730;
        startX = startX; //Workaround for initial position inside update
        var blockWidth = 80; //Width of blocks and floor spaces
        var blockHeight = 40; //Height of blocks and floor spaces
        for(var i=0;i<9;i++){
            game.add.image(i*100, 501, 'floor');
        }
                
        //Control variables
        clicked = false; //Floor blocks or apilled blocks clicked
        hideLabels = false; //Labels of blocks
        animate = false; //Start move animation
        checkCollide = false; //Check if tractor fon't any block left or floor hole
        result = false; //Game is correct
        move = false; //Continue tractor animation
        moveCounter = 0; //Move counter
        moveEnd = 140; //Move end counter
                
        //tractor
        var tractorAlign = -80;
        if(oneOperator=='Minus'){
            tractorAlign *= -1;
        } 
        tractor = game.add.sprite(startX+tractorAlign, 445, 'tractor');
        tractor.anchor.setTo(0.5, 0.5);
        tractor.scale.setTo(0.8);
        tractor.animations.add('right',[0,1,2,3,4]);
        if(oneOperator=='Minus'){
            tractor.scale.x *= -1;
        }
        
         //generator
        //Blocks and fractions
        console.log("pos " +onePosition);
        maxBlocks = onePosition+4; //Maximum blocks
        if(oneType=='B' || oneOperator=='Mixed') maxBlocks = 10;
        blocks = game.add.group(); //Fraction arrays (apilled)
        numBlocks = game.rnd.integerInRange(onePosition+2, maxBlocks); //Number of blocks
        console.log("num " + numBlocks+", min " + (onePosition+2) + ", max " + maxBlocks);
        curBlock = 0; //Actual index block
        blockDirection = []; //Directions right(plus), left (minus)
        blockDistance = []; //Displacement distance of the blocks
        blockLabel = game.add.group(); //Labels of the blocks
        blockSeparator = game.add.group(); //Separator of the labels
        //blockAngle = []; //Angles of blocks
        //blockTraceColor = []; //Trace colors
        endPosition = startX; //Ending position, accumulative
        if(oneOperator=='Minus') endPosition -= blockWidth;
        else endPosition += blockWidth;
        //Game A exclusive variables 
        floorBlocks = game.add.group(); //Selectable floor blocks
        floorIndex = -1; //Selected floor block
        floorCount = 8; //Number of floor blocks
        floorClicked = false; //If clicked portion of floor
        curFloor = -1;
        //Game B exclusive variables
        arrowPlace = startX; //Fixed place for help arrow
        if(oneOperator=='Minus') arrowPlace  -= blockWidth;
        else arrowPlace += blockWidth;
        fractionClicked = false; //If clicked a fraction (game B)
        fractionIndex = -1; //Index of clicked fraction (game B)
        
        hasFigure = false;
        for(var p=0;p<numBlocks;p++){

            var portion = game.rnd.integerInRange(1, oneDifficulty); //Portion of the square, according to difficulty
            if(portion==3) detail+= "4,";
            else detail += portion+",";
            
            if(portion==oneDifficulty) hasFigure = true;
            var direction = '';
            var lineColor = '';
            
            if(oneOperator=='Plus'){
                direction = 'Right';    
                lineColor = 0x31314e;
            }else if(oneOperator=='Minus'){
                direction = 'Left';
                lineColor = 0xb30000;
            }
            
            var block = game.add.graphics(startX, 460-p*blockHeight);
                block.anchor.setTo(0.5, 0.5);
                block.lineStyle(2, lineColor);
                block.beginFill(0xefeff5);
            
            blockDirection[p] = direction;
            if(portion==1){
                block.drawRect(0, 0, blockWidth, blockHeight);
                
                blockDistance.push(blockWidth);
                //blockAngle.push(360);

                if(oneLabel){
                    var labelX = startX;
                    if(oneOperator=='Minus') labelX -= (15+blockWidth);
                    else labelX += blockWidth+15;
                    var label = game.add.text(labelX, 480-p*blockHeight, portion , styleLabel);
                    label.anchor.setTo(0.5, 0.5);
                    blockLabel.add(label);
                }
            }else{
                if(portion==3) portion = 4;
                var distance = blockWidth/portion;
                
                block.drawRect(0, 0, distance, blockHeight);
                
                blockDistance.push(distance);

                if(oneLabel){
                    var labelX = startX;
                    if(oneOperator=='Minus') labelX -= (15+distance);
                    else labelX += 15+distance;
                    var separator = game.add.sprite(labelX, 480-p*blockHeight, 'separator');
                    separator.scale.setTo(0.6);
                    separator.anchor.setTo(0.5, 0.5);
                    blockSeparator.add(separator);
                    var label = game.add.text(labelX, 483-p*blockHeight, '1\n'+portion , styleFraction);
                    label.anchor.setTo(0.5, 0.5);
                    blockLabel.add(label);
                }
            }

            if(direction=='Right'){
                endPosition += Math.floor(blockWidth/portion);
            }else if(direction=='Left'){
                endPosition -= Math.floor(blockWidth/portion);
                block.scale.x *= -1;
            }

            block.endFill();
            
            //If game is type B, (select fractions, adding event)
            if(oneType=='B'){
                block.alpha = 0.5;
                block.inputEnabled = true;
                block.input.useHandCursor = true;
                block.events.onInputDown.add(this.clickSquare, {indice: p});
                block.events.onInputOver.add(this.overSquare, {indice: p});
                block.events.onInputOut.add(this.outSquare, {indice: p});
            }
            
            blocks.add(block);
        }
        
        //Calculate next block
        if(blockDirection[curBlock]=='Right'){
            nextEnd = startX+blockDistance[curBlock];
        }else{
            nextEnd = startX-blockDistance[curBlock];
        }
        
        //If end position is out of bounds, restart
        if(!hasFigure) game.state.start('gameSOne');
        
        if (oneOperator=='Plus' && (endPosition<(startX+blockWidth) || endPosition>(startX+8*blockWidth))){
            game.state.start('gameSOne');
        }else if (oneOperator=='Minus' && (endPosition>(startX) || endPosition<(startX-(8*blockWidth)))){
            game.state.start('gameSOne');
        }
        
        //If game is type B, selectiong a random block floor place
        if(oneType=='B'){
            var end = game.rnd.integerInRange(1, numBlocks);
            for(var i=0;i<end;i++){
                if(blockDirection[i]=='Right')
                    arrowPlace += blockDistance[i];
                else if(blockDirection[i]=='Left')
                    arrowPlace -= blockDistance[i];
            }
        }
        
        //Selectable floor
        floorCount = 8*oneDifficulty;
        
        var widFloor = blockWidth/oneDifficulty;
        if(oneDifficulty==3){
            floorCount = 8*4;
            widFloor = blockWidth/4;
        }
        
        for(var i = 0; i<floorCount; i++){
            var posX = startX;
            
            if(oneOperator=='Minus') posX -= (blockWidth + i*widFloor);
            else posX += (blockWidth + i*widFloor);
            
            if(oneType=='B'){
                if(oneOperator=='Minus'){
                    if(posX<=arrowPlace){
                        floorCount = i+1;
                        floorIndex = i-1;
                        break;
                    }
                }else{
                    if(posX>=arrowPlace){
                        floorCount = i+1;
                        floorIndex = i-1;
                        break;
                    }
                }
            }
            var block = game.add.graphics(posX, 500);
                block.anchor.setTo(0.5, 0);
                block.lineStyle(0.2, 0xffffff);
                block.beginFill(0x000000);
                block.drawRect(0, 0, widFloor, blockHeight);
                block.endFill();
            if(oneOperator=='Minus') block.scale.x *= -1;
            
            if(oneType=="A"){
                block.alpha = 0.5;
                block.inputEnabled = true;
                block.input.useHandCursor = true;
                block.events.onInputDown.add(this.clickSquare, {indice: i});
                block.events.onInputOver.add(this.overSquare, {indice: i});
                block.events.onInputOut.add(this.outSquare, {indice: i});
            }
            
            floorBlocks.add(block);     
        }
        
        for(var i=0;i<=8;i++){
            var posX = startX;
            if(oneOperator=='Minus')posX -= ((9-i)*blockWidth);
            else posX+=((i+1)*blockWidth);
            
            game.add.text(posX, 560, i , stylePlace).anchor.setTo(0.5, 0.5); 
        }
        
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
            m_back.events.onInputDown.add(this.loadState, {state: "menuSOne", beep: beepSound});
            m_back.events.onInputOver.add(this.showOption, {message: words.menu_back});
            m_back.events.onInputOut.add(this.showOption, {message: ""});
        }
         // Help button
        /*
        m_help = game.add.sprite(160, 10, 'help');
        m_help.inputEnabled = true;
        m_help.input.useHandCursor = true;
        m_help.events.onInputDown.add(this.viewHelp, {beep: this.beepSound});
        m_help.events.onInputOver.add(this.showOption, {message: words.menu_help});
        m_help.events.onInputOut.add(this.showOption, {message: ""});
        */
        //ok and error images
        okImg = game.add.image(game.world.centerX, game.world.centerY, 'h_ok');
        okImg.anchor.setTo(0.5);
        okImg.alpha = 0;
        errorImg = game.add.image(game.world.centerX, game.world.centerY, 'h_error');
        errorImg.anchor.setTo(0.5);
        errorImg.alpha = 0;
        
         //Help arrow
        arrow = game.add.sprite(this.arrowPlace, 480, 'down');
        arrow.anchor.setTo(0.5, 0.5);
        if(oneType=="B")
            arrow.alpha = 0;
        else if(oneType=="A")
            arrow.alpha = 0.5;
    },
    
    updateCounter: function() {
        totalTime++;
    },
    
    overSquare: function(){
        if(!clicked){
            if(oneType=="A"){
                for(var i=0;i<floorCount;i++){
                    if(i<=this.indice){
                        floorBlocks.children[i].alpha = 1;
                    }else{
                        floorBlocks.children[i].alpha = 0.5;
                    }
                }
                floorIndex = this.indice;
            }else if(oneType=="B"){
                
                for(var i=0;i<numBlocks;i++){
                    if(i<=this.indice){
                        blocks.children[i].alpha = 1;
                    }else{
                        blocks.children[i].alpha = 0.5;
                    }
                }
                blockIndex = this.indice;
            }
        }
    },
    outSquare: function(){
        if(!clicked){
            if(oneType=="A"){
                for(var i=0;i<floorCount;i++){
                    floorBlocks.children[i].alpha = 0.5;
                }
                floorIndex = -1;
            }else if(oneType=="B"){
                for(var i=0;i<=this.indice;i++){
                    blocks.children[i].alpha = 0.5;
                }
                blockIndex = -1;
            }
        }
    },
    
    clickSquare: function(){
        if(!clicked && !move){
            
            if(oneType=='A'){
    
                arrow.alpha = 1;
                clicked = true;
                animate = true;
                beepSound.play();
                
                tractor.animations.play('right', 5, true);
                
                if(oneLabel){ //Hiding labels
                    blockLabel.visible = false;
                    blockSeparator.visible = false;
                }
                
                //cleaning path
                if(oneOperator=='Minus'){
                    for(var i=0; i< floorCount; i++){
                        if(i>floorIndex){
                            floorBlocks.children[i].alpha = 0;
                        }
                    }
                }else{
                    for(var i=0; i< floorCount; i++){
                        if(i>floorIndex){
                            floorBlocks.children[i].alpha = 0;
                        }
                    }
                }
                    
                blockIndex = numBlocks - 1;
            }else if(oneType=='B'){ //Delete unselected blocks

                var minusBlocks = 0;
                for(var i=0;i<numBlocks;i++){
                    if(i<=blockIndex){
                        blocks.children[i].alpha = 1;
                    }else{
                        blocks.children[i].visible = false; //Delete unselected block
                        minusBlocks +=1; //number of blocks to reduce
                    }
                }
                numBlocks -= minusBlocks; //Final reduced blocks
                
                arrow.alpha = 1;
                clicked = true;
                animate = true;
                beepSound.play();
                tractor.animations.play('right', 5, true);

                if(oneLabel){ //Hiding labels
                    blockLabel.visible = false;
                    blockSeparator.visible = false;
                }
            }
        }
    },
    postScore: function (){
        
        // ::Igor
        var fi = 0;
        if (result == true) { // Correct student's result:
            hits[onePosition - 1] ++;
            end[onePosition - 1] = Math.floor(Date.now()/1000);
            conta = true;
            if (onePosition == 4) {
                fi = 1;
            }
        } else { // Error student's result:
            errors[onePosition - 1] ++;
        }
        iterator = onePosition;
        sendResults(fi);
        
        /*var abst = "numBlocks:"+numBlocks+", valBlocks: " + detail+" blockIndex: " + blockIndex + ", floorIndex: " + floorIndex;
        
        var hr = new XMLHttpRequest();
        // Create some variables we need to send to our PHP file
        var url = "resource/cn/save.php";
        var vars = "s_ip="+hip+"&s_name="+name+"&s_lang="+lang+"&s_game="+oneShape+"&s_mode="+oneType;
        vars += "&s_oper="+oneOperator+"&s_leve="+oneDifficulty+"&s_posi="+onePosition+"&s_resu="+result+"&s_time="+totalTime+"&s_deta="+abst;
        
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
                
        if(!clicked){
            if(!move){
                if(oneType=='A'){
                    //Follow mouse
                    if (game.physics.arcade.distanceToPointer(arrow, game.input.activePointer) > 8){
                        var xPos = game.input.mousePointer.x;
                        arrow.x = xPos;
                    }                    
                }
            }
        }
        
        //Start animation
        if(animate){

            if(blockDirection[curBlock]=='Right'){
                tractor.x+=2;
            }else if(blockDirection[curBlock]=='Left'){
                tractor.x-=2;
            }
                        
            for(var i=0;i<numBlocks;i++){ //Moving every block
                if(blockDirection[curBlock]=='Right'){
                    blocks.children[i].x +=2;
                }else{
                    blocks.children[i].x -=2;
                }
            }
            
            var extra = 80-blockDistance[curBlock];
            
            if(blockDirection[curBlock]=='Right'){
                if(blocks.children[curBlock].x>=nextEnd+extra){
                    blocks.children[curBlock].alpha = 0;
                    blocks.y += 40;
                    curBlock +=1;
                    nextEnd += blockDistance[curBlock];
                    for(var i=0; i<=floorIndex; i++ ){
                        if(floorBlocks.children[i].x<(blocks.children[curBlock-1].x+blockDistance[curBlock-1])){
                            floorBlocks.children[i].alpha = 0.2;
                            curFloor = i;
                        }
                    }
                }
            }else if(blockDirection[curBlock]=='Left'){
                if(blocks.children[curBlock].x<=(nextEnd-extra)){
                    blocks.children[curBlock].alpha = 0;
                    blocks.y += 40;
                    curBlock+=1;
                    nextEnd -= blockDistance[curBlock];
                    for(var i=0; i<=floorIndex; i++ ){
                        if(floorBlocks.children[i].x>(blocks.children[curBlock-1].x-blockDistance[curBlock-1])){
                            floorBlocks.children[i].alpha = 0.2;
                            curFloor = i;
                        }
                    }
                }
            }
            
            if( curBlock>blockIndex || curFloor>=floorIndex){ //Final position
                animate= false;
                checkCollide = true;
            }       
        }
        
        //Check if tractor has blocks left or floor holes
        if(checkCollide){
            tractor.animations.stop();
            timer.stop();
            //Check left blocks
            var resultBlock = true;
            for(var i=0; i<=blockIndex; i++){
                if(blocks.children[i].alpha==1) resultBlock = false;
            }
            
            //check floor Holes
            var resultFloor = true;
            for(var i=0; i<=floorIndex; i++){
                if(floorBlocks.children[i].alpha==1) resultFloor = false;
            }
                        
            if(resultBlock && resultFloor){
                result = true;
            }else{
                result = false;
            }
            this.postScore();
            move = true;
            checkCollide = false;
        }
        
        //Continue moving animation
        if(move){
            
            if(moveCounter==0){
                if(result){
                    tractor.animations.play('right', 6, true);
                    okSound.play();
                    okImg.alpha = 1;
                }else{
                    errorSound.play();
                    errorImg.alpha = 1;
                }
            }
            
            moveCounter += 1;
            
            if(result){
                if(oneOperator=='Minus'){
                    tractor.x -=2;
                }else{
                    tractor.x +=2;
                }
            }
            
            if(moveCounter>=moveEnd){
                if(result){
                    oneMove = true;
                }else{
                    oneMove = false;
                }
                game.state.start('mapSOne');
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
        
    viewHelp: function(){
        if(!clicked){
            var pointer;
            if(oneType=='A'){
                var pointer = game.add.image(endPosition, 490, 'pointer');
            }else{
                var pointer = game.add.image(blocks.children[endIndex-1].x, blocks.children[endIndex-1].y-blockSize/2, 'pointer');
            }
            pointer.anchor.setTo(0.5, 0);
            pointer.alpha = 0.7;
        }
    }
    
};
/****************************** END ****************************/
var endSquareOne={
    create: function() {  
        
        // Creating sound variable
        this.beepSound = game.add.audio('sound_beep');
        this.okSound = game.add.audio('sound_ok');
        this.errorSound = game.add.audio('sound_error');
        
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
            var block = game.add.image(680+(p-1)*30, 10, 'block');
            block.scale.setTo(2, 1); //Scaling to double width
        }
        game.add.text(830, 10, '100%', styleMenu);
        game.add.text(670, 10, words.difficulty + ' ' + oneDifficulty, styleMenu).anchor.setTo(1,0);
        game.add.image(680, 10, 'pgbar');
        
        //Farm and trees
        game.add.sprite(650, 260 , 'farm').scale.setTo(1.1);
        game.add.sprite(30, 280 , 'tree4');
        game.add.sprite(360, 250 , 'tree2');
        
        //tractor
        this.tractor = game.add.sprite(0, 490 , 'tractor');
        this.tractor.anchor.setTo(0.5,0.5);
        this.tractor.scale.setTo(0.8);
            
        this.tractor.animations.add('right',[0,1,2,3,4]);
        this.tractor.animations.play('right', 5, true);
        
    },

    update: function() {
        if(this.tractor.x<=700){
            this.tractor.x += 2;
        }else{
            if(oneMenu){
                // REDIRECIONAR AQUI!! ::Igor
                if (redir == true) {
                    this.tractor.animations.stop();
                    finish_redirect();
                    redir = false;
                }
            }else{
                this.tractor.animations.stop();
            }
        }
    },
    
    verPrincipal: function(){
        game.state.start('welcome');
    },
    
    verMenu: function(){
        if(oneMenu){
            game.state.start('menu');
        }
    }        
};
