// Kid and Circle states, games 1 and 2

/****************************** MENU ****************************/
var stairsPlus, stairsMinus, stairsMixed;

var menuCircleOne={
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
        var stairWidth = 85; //Width of a stair
        var startStair = 240;
        var startSymbol = 150;
        var startCircle = (startSymbol/2)+startStair+stairWidth*5;
        
         //First stairs, plus, 5 levels, blue circle
        var blueCircle = game.add.graphics(startCircle, 195);
            blueCircle.anchor.setTo(0.5,0.5);
            blueCircle.lineStyle(2, 0x31314e);
            blueCircle.beginFill(0xefeff5);
            blueCircle.drawCircle(0, 0, 60);
            blueCircle.endFill();
        var r_arrow = game.add.sprite(startSymbol, 195, 'h_arrow'); 
            r_arrow.scale.setTo(0.7);
            r_arrow.anchor.setTo(0.5,0.5);
        
        stairsPlus = [];
        for(var i=1;i<=5;i++){
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
        
        //Second stairs, minus, 5 levels, red circle
        var redCircle = game.add.graphics(startCircle, 350);
            redCircle.anchor.setTo(0.5,0.5);
            redCircle.lineStyle(2, 0xb30000);
            redCircle.beginFill(0xefeff5);
            redCircle.drawCircle(0, 0, 60);
            redCircle.endFill();
        var l_arrow = game.add.sprite(startSymbol, 350, 'h_arrow');
            l_arrow.scale.setTo(-0.7, 0.7);
            l_arrow.anchor.setTo(0.5,0.5);
        
        var stairsMinus = [];
        for(var i=1;i<=5;i++){
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
        
        //Thrid stairs, mixed, 5 levels, two circles
        var bCircle = game.add.graphics(startCircle-30, 500);
            bCircle.anchor.setTo(0.5,0.5);
            bCircle.lineStyle(2, 0x31314e);
            bCircle.beginFill(0xefeff5);
            bCircle.drawCircle(0, 0, 60);
            bCircle.endFill();
        
        var rCircle = game.add.graphics(startCircle+40, 500);
            rCircle.anchor.setTo(0.5,0.5);
            rCircle.lineStyle(2, 0xb30000);
            rCircle.beginFill(0xefeff5);
            rCircle.drawCircle(0, 0, 60);
            rCircle.endFill();
        
        var d_arrow = game.add.sprite(startSymbol, 500, 'h_double'); 
            d_arrow.scale.setTo(0.7);
            d_arrow.anchor.setTo(0.5,0.5);
        
        var stairsMixed = [];
        for(var i=1;i<=5;i++){
            //stair
            var x1 = startStair+(stairWidth*(i-1));
            var y1 = 435+maxHeight-i*stairHeight;
            var x2 = stairWidth;//x1 + 40;
            var y2 = stairHeight*i;//y1 + 24;
            
            stairsMixed[i] = game.add.graphics(0, 0);
            stairsMixed[i].lineStyle(1, 0xFFFFFF, 1);
            stairsMixed[i].beginFill(0xb366ff);
            stairsMixed[i].drawRect(x1, y1, x2, y2);
            stairsMixed[i].endFill();
            
            //event
            stairsMixed[i].inputEnabled = true;
            stairsMixed[i].input.useHandCursor = true;
            stairsMixed[i].events.onInputDown.add(this.loadMap, {beep: beepSound, difficulty: i, operator: 'Mixed' });
            stairsMixed[i].events.onInputOver.add(function (item) { item.alpha=0.5; }, this);
            stairsMixed[i].events.onInputOut.add(function (item) { item.alpha=1; }, this);
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
        game.state.start('mapCOne');
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
            game.state.start('mapCOne');
        }else{
            game.state.start('unofinal');
        }
    }
    
};

/****************************** MAP ****************************/
var mapCircleOne={
    create: function() {
        
        // Creating sound variable
        beepSound = game.add.audio('sound_beep');
        
        // Reading dictionary
        var words = game.cache.getJSON('dictionary');

        // Background
        game.add.image(0, 40, 'bgmap');
        
        if(oneMenu){ //IF not url game
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
            m_back.events.onInputDown.add(this.loadState, {state: "menuCOne", beep: beepSound});
            m_back.events.onInputOver.add(this.showOption, {message: words.menu_back});
            m_back.events.onInputOut.add(this.showOption, {message: ""});
        }
        
        // Styles for labels
        var stylePlace = { font: '26px Arial', fill: '#ffffff', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        // Progress bar
        var percentText = onePosition*25;
        var percentBlocks = onePosition;
        for(var p=0;p<percentBlocks;p++){
            var block = game.add.image(680+p*37, 10, 'block');
            block.scale.setTo(2.5, 1); //Scaling to double width
        }
        game.add.text(840, 10, percentText+'%', styleMenu);
        game.add.text(670, 10, words.difficulty + ' ' + oneDifficulty, styleMenu).anchor.setTo(1,0);
        game.add.image(680, 10, 'pgbar');
        
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

        // Kid start position
        this.kid = game.add.sprite(this.points.x[onePosition], this.points.y[onePosition], 'kid_run');
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
                this.kid, 
                this.points.x[onePosition+1],
                this.points.y[onePosition+1],
                100
            );
            
            // I kid reached the end, stop movement
            if(Math.ceil(this.kid.x)==this.points.x[onePosition+1] || Math.ceil(this.kid.y)==this.points.y[onePosition+1]){
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
            game.state.start('gameCOne');
        }else{
            game.state.start('endCOne');
        }
    }
};

/****************************** GAME ****************************/
var okSound, errorSound; //sounds
var startX; //start position

var clicked, hideLabels, animate, checkCollide, result, hasFigure; //control variables
var fly, flyCounter, flyend; //flyvariables
var trace; //circle trace
var kid_walk, balloon, basket;

//Balloon and blocks control
var maxBlocks, blockSize, blocks, numBlocks, curBlock, blockDirection, blockDistance, blockLabel, blockSeparator, blockAngle, blockTraceColor, endPosition;

var balloonPlace, fractionClicked, fractionIndex, numPlus, endIndex;

var okImg, errorImg;

var detail;
var gameCircleOne={
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
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        //Floor and road
        startX = 66; //Initial kid and place position
        if(oneOperator=='Minus') startX = 66+5*156;
        
        placeDistance = 156; //Distance between places
        blockSize = 60;
        for(var i=0;i<9;i++){
            game.add.image(i*100, 501, 'floor');
        }
        var road = game.add.image(47, 515, 'road');
        road.scale.setTo(1.01,0.94);
        if(oneType=='A'){
            road.inputEnabled = true;
            road.events.onInputDown.add(this.setPlace, {beep: beepSound}); //enabling input for tablets
        }
        
        for(var p=0;p<=5;p++){// Places
            var place = game.add.image(66+p*placeDistance, 526, 'place_a');
            place.anchor.setTo(0.5);
            place.scale.setTo(0.3);
            game.add.text(66+p*placeDistance, 560, p , stylePlace).anchor.setTo(0.5); 
        }
        
        //Control variables
        clicked = false; //Air ballon positioned
        hideLabels = false; //Labels animations
        animate = false; //Start move animation
        checkCollide = false; //Check kid inside ballon's basket
        result = false; //Game is correct
        fly = false; //Start ballon fly animation
        flyCounter = 0; //Fly counter
        flyEnd = 140; //Fly end counter
        hasFigure = false; //If has level figure
        //trace
        trace = this.add.bitmapData(this.game.width, this.game.height);
        trace.addToWorld();
        trace.clear();
                
         //generator
        //Circles and fractions
        var maxBlocks = onePosition+1; //Maximum blocks according to difficulty
        if(oneType=='B' || oneOperator=='Mixed') maxBlocks = 6;
        blocks = game.add.group(); //Fraction arrays
        numBlocks = game.rnd.integerInRange(onePosition, maxBlocks); //Number of blocks
        curBlock = 0; //Actual index block
        blockDirection = []; //Directions right(plus), left (minus)
        blockDistance = []; //Displacement distance of the blocks
        blockLabel = game.add.group(); //Labels of the blocks
        blockSeparator = game.add.group(); //Separator of the labels
        blockAngle = []; //Angles of blocks
        blockTraceColor = []; //Trace colors
        endPosition = startX; //Ending position, accumulative
        
        //Game B exclusive variables
        balloonPlace = this.game.world.centerX; //Fixed place for ballon (game B)
        fractionClicked = false; //If clicked a fraction (game B)
        fractionIndex = -1; //Index of clicked fraction (game B)
        numPlus = game.rnd.integerInRange(1, numBlocks-1);
        
        for(var p=0;p<numBlocks;p++){

            var portion = game.rnd.integerInRange(1, oneDifficulty); //Portion of the circle, according to difficulty
            detail += portion+",";
            
            if(portion==oneDifficulty){
                hasFigure = true;
            }
            
            var direction = '';
            var lineColor = '';
            if(oneOperator=='Mixed'){
                if(p<=numPlus){
                    direction = 'Right';
                    lineColor = 0x31314e;
                }else{
                    direction = 'Left';
                    lineColor = 0xb30000;
                }
                /*var directions = ['Right','Left'];
                var rndIndex = game.rnd.integerInRange(0, 1);
                direction = directions[rndIndex];
                if(rndIndex==0) lineColor = 0x31314e;
                else lineColor = 0xb30000;*/
            }else if(oneOperator=='Plus'){
                direction = 'Right';    
                lineColor = 0x31314e;
            }else if(oneOperator=='Minus'){
                direction = 'Left';
                lineColor = 0xb30000;
            }
            
            blockTraceColor[p] = lineColor;
            var block = game.add.graphics(startX, 490-p*blockSize);
                block.anchor.setTo(0.5,0.5);

                block.lineStyle(2, lineColor);
                block.beginFill(0xefeff5);
            
            if (direction == 'Right')  block.scale.y *= -1;

            blockDirection[p] = direction;
                        
            if(portion==1){
                block.drawCircle(0, 0, blockSize);

                blockDistance.push(placeDistance);
                blockAngle.push(360);

                if(oneLabel){
                    var labelX = startX;
                    if(oneOperator=='Minus') labelX -= 65;
                    else labelX += 65;
                    var label = game.add.text(labelX, 490-p*blockSize, portion , styleLabel);
                    label.anchor.setTo(0.5, 0.5);
                    blockLabel.add(label);
                }
            }else{
                var distance = 360/portion+5;
                block.arc(0, 0, blockSize/2, game.math.degToRad(distance), 0, true);

                blockDistance.push(Math.floor(placeDistance/portion));
                blockAngle.push(distance);

                if(oneLabel){
                    var labelX = startX;
                    if(oneOperator=='Minus') labelX -= 65;
                    else labelX += 65;
                    var separator = game.add.sprite(labelX, 485-p*blockSize, 'separator');
                    separator.anchor.setTo(0.5, 0.5);
                    blockSeparator.add(separator);
                    var label = game.add.text(labelX, 488-p*blockSize, '1\n'+portion , styleLabel);
                    label.anchor.setTo(0.5, 0.5);
                    blockLabel.add(label);
                }
            }

            if(direction=='Right'){
                endPosition += Math.floor(placeDistance/portion);
            }else if(direction=='Left'){
                endPosition -= Math.floor(placeDistance/portion);
            }

            block.endFill();
            block.angle +=90;
            
            //If game is type B, (select fractions, adding event)
            if(oneType=='B'){
                block.alpha = 0.5;
                block.inputEnabled = true;
                block.input.useHandCursor = true;
                block.events.onInputDown.add(this.clickCircle, {indice: p});
                block.events.onInputOver.add(this.overCircle, {indice: p});
                block.events.onInputOut.add(this.outCircle, {indice: p});
            }
            
            blocks.add(block);
        }
        
        //Calculate next block
        if(blockDirection[curBlock]=='Right'){
            nextEnd = startX+blockDistance[curBlock];
        }else{
            nextEnd = startX-blockDistance[curBlock];
        }
        
        //If game is type B, selectiong a random balloon place
        
        if(oneType=='B'){
            balloonPlace = startX;
            endIndex = game.rnd.integerInRange(numPlus, numBlocks);
            for(var i=0;i<endIndex;i++){
                if(blockDirection[i]=='Right')
                    balloonPlace += blockDistance[i];
                else if(blockDirection[i]=='Left')
                    balloonPlace -= blockDistance[i];
            }
            if(balloonPlace<66 || balloonPlace>66+5*placeDistance || !hasFigure){
                game.state.start('gameCOne');
            }
        }
        
        //If end position is out of bounds, restart
        if (endPosition<66 || endPosition>66+3*260 || !hasFigure){
            game.state.start('gameCOne');
        }
        //kid
        kid_walk = game.add.sprite(startX, 495-numBlocks*blockSize, 'kid_walk');
        kid_walk.anchor.setTo(0.5, 0.8);
        kid_walk.scale.setTo(0.8);
        kid_walk.animations.add('right',[0,1,2,3,4,5,6,7,8,9,10,11]);
        kid_walk.animations.add('left',[23,22,21,20,19,18,17,16,15,14,13,12]);
        if(oneOperator=='Minus'){
            kid_walk.animations.play('left', 6, true);
            kid_walk.animations.stop();
        }
        //globo
        balloon = game.add.sprite(balloonPlace, 350, 'balloon');
        balloon.anchor.setTo(0.5, 0.5);
        balloon.alpha = 0.5;
        basket = game.add.sprite(balloonPlace, 472, 'balloon_basket');
        basket.anchor.setTo(0.5, 0.5);
        
        // Menu options
          //information label
        m_info = game.add.text(14, 53, "", { font: "20px Arial", fill: "#330000", align: "center" });
        
        if(oneMenu){
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
            m_back.events.onInputDown.add(this.loadState, {state: "menuCOne", beep: beepSound});
            m_back.events.onInputOver.add(this.showOption, {message: words.menu_back});
            m_back.events.onInputOut.add(this.showOption, {message: ""});
        }
         // Help button
        m_help = game.add.sprite(160, 10, 'help');
        m_help.inputEnabled = true;
        m_help.input.useHandCursor = true;
        m_help.events.onInputDown.add(this.viewHelp, {beep: this.beepSound});
        m_help.events.onInputOver.add(this.showOption, {message: words.menu_help});
        m_help.events.onInputOut.add(this.showOption, {message: ""});
        
        //ok and error images
        okImg = game.add.image(game.world.centerX, game.world.centerY, 'h_ok');
        okImg.anchor.setTo(0.5);
        okImg.alpha = 0;
        errorImg = game.add.image(game.world.centerX, game.world.centerY, 'h_error');
        errorImg.anchor.setTo(0.5);
        errorImg.alpha = 0;
    },
    
    updateCounter: function() {
        totalTime++;
    },
        
    overCircle: function(){
        
        if(!clicked){
            for(var i=0;i<numBlocks;i++){
                if(i<=this.indice){
                    blocks.children[i].alpha = 1;
                }else{
                    blocks.children[i].alpha = 0.5;
                }
            }
        }

    },
    outCircle: function(){
        if(!clicked){
            for(var i=0;i<=this.indice;i++){
                blocks.children[i].alpha = 0.5;
            }
        }
    },
    
    clickCircle: function(){
        if(!clicked){
            var minusBlocks = 0;
            
            for(var i=0;i<numBlocks;i++){
                if(i<=this.indice){
                    fractionIndex = this.indice;
                    blocks.children[i].alpha = 1;
                }else{
                    blocks.children[i].visible = false; //Delete unselected block
                    minusBlocks +=1; //number of blocks to reduce
                    kid_walk.y += blockSize; //Lowering kid
                }
            }
            
            numBlocks -= minusBlocks; //Final reduced blocks

            balloon.alpha = 1;
            clicked = true;
            animate = true;
            beepSound.play();
            if(blockDirection[curBlock]=='Right'){
                kid_walk.animations.play('right', 6, true);
            }else{
                kid_walk.animations.play('left', 6, true);
            }

            if(oneLabel){ //Hiding labels
                blockLabel.visible = false;
                blockSeparator.visible = false;
            }
        }
    },

    setPlace: function(){
        if(!clicked){
            
            balloon.x = game.input.x;
            basket.x = game.input.x;

            balloon.alpha = 1;
            clicked = true;
            animate = true;
            beepSound.play();
            if(blockDirection[curBlock]=='Right'){
                kid_walk.animations.play('right', 6, true);
            }else{
                kid_walk.animations.play('left', 6, true);
            }

            if(oneLabel){ //Hiding labels
                blockLabel.visible = false;
                blockSeparator.visible = false;
            }
        }
    },
    postScore: function (){
        
        /*var abst = "numCircles:"+numBlocks+", valCircles: " + detail+" balloonX: " + basket.x + ", selIndex: " + fractionIndex;
        
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
    },

    update: function() {
        
        if (game.input.activePointer.isDown && !fly && !clicked){
            //Positionate balloon - Game A
            if(oneType=='A'){
                if(game.input.mousePointer.y>60){ //Dead zone for click
                    balloon.x = game.input.mousePointer.x;
                    balloon.alpha = 1;
                    clicked = true;
                    animate = true;
                    beepSound.play();
                    if(blockDirection[curBlock]=='Right'){
                        kid_walk.animations.play('right', 6, true);
                    }else{
                        kid_walk.animations.play('left', 6, true);
                    }

                    if(oneLabel){ //Hiding labels
                        blockLabel.visible = false;
                        blockSeparator.visible = false;
                    }
                }
            }
        }
        
        if(!clicked){
            if(!fly){
                if(oneType=="A"){
                    //Follow mouse
                    if (game.physics.arcade.distanceToPointer(balloon, game.input.activePointer) > 8){
                        balloon.x = game.input.mousePointer.x;
                        basket.x = game.input.mousePointer.x;
                    }
                }
            }
        }
        
        
        //Start animation
        if(animate){
            
            var color = '';
            if(blockDirection[curBlock]=='Right'){
                kid_walk.x+=2;
                color = 'rgba(0, 51, 153, 1)';
            }else if(blockDirection[curBlock]=='Left'){
                kid_walk.x-=2;
                color = 'rgba(179, 0, 0, 1)';
            }
            
            trace.rect(kid_walk.x, 526, 2, 2, color);
            
            for(var i=0;i<numBlocks;i++){ //Moving every block
                if(blockDirection[curBlock]=='Right'){
                    blocks.children[i].x +=2;
                }else{
                    blocks.children[i].x -=2;
                }
            }
            
            blockAngle[curBlock] -= 4.6;
            blocks.children[curBlock].clear();
            blocks.children[curBlock].lineStyle(2, blockTraceColor[curBlock]);
            blocks.children[curBlock].beginFill(0xefeff5);
            blocks.children[curBlock].arc(0, 0, blockSize/2, game.math.degToRad(blockAngle[curBlock]), 0, true);
            blocks.children[curBlock].endFill();
            
            if(blockDirection[curBlock]=='Right'){
                if(blocks.children[curBlock].x>=nextEnd){
                    blocks.children[curBlock].visible = false;
                    blocks.y += blockSize;
                    kid_walk.y += blockSize;
                    curBlock+=1;
                    if(blockDirection[curBlock]=='Right'){
                        nextEnd += blockDistance[curBlock];
                        kid_walk.animations.play('right', 6, true);
                    }else if(blockDirection[curBlock]=='Left'){
                        nextEnd -= blockDistance[curBlock];
                        kid_walk.animations.play('left', 6, true);
                    }
                }
            }else{
                if(blocks.children[curBlock].x<=nextEnd){
                    blocks.children[curBlock].visible = false;
                    blocks.y += blockSize;
                    kid_walk.y += blockSize;
                    curBlock+=1;
                    if(blockDirection[curBlock]=='Right'){
                        nextEnd += blockDistance[curBlock];
                        kid_walk.animations.play('right', 6, true);
                    }else if(blockDirection[curBlock]=='Left'){
                        nextEnd -= blockDistance[curBlock];
                        kid_walk.animations.play('left', 6, true);
                    }
                }
            }
            
            if(curBlock==numBlocks ){ //Final position
                animate= false;
                checkCollide = true;
            }       
        }
        
        //Check if kid is inside the basket
        if(checkCollide){
            kid_walk.animations.stop();
            timer.stop();
            if(this.checkOverlap(basket,kid_walk)){
                result = true;
            }else{
                result = false;
            }
            this.postScore();
            fly = true;
            checkCollide = false;
        }
        
        //Fly animation
        if(fly){
            
            if(flyCounter==0){
                if(result){
                    okSound.play();
                    okImg.alpha = 1;
                }else{
                    errorSound.play();
                    errorImg.alpha = 1;
                }
            }
            
            flyCounter += 1;
            balloon.y -= 2;
            basket.y -= 2;
            
            if(result){
                kid_walk.y -=2;
            }
            
            if(flyCounter>=flyEnd){
                if(result){
                    oneMove = true;
                }else{
                    oneMove = false;
                }
                game.state.start('mapCOne');
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
    },
    
    checkOverlap: function (spriteA, spriteB) {
        var xA = spriteA.x;
        var xB = spriteB.x;
                
        if(Math.abs(xA-xB)>25){
            return false;
        }else{
            return true;
        }
    }
    
};
/****************************** END ****************************/
var endCircleOne={
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
        this.kid = game.add.sprite(0, -152 , 'kid_run');
        this.kid.anchor.setTo(0.5,0.5);
        this.kid.scale.setTo(0.7);
        var walk = this.kid.animations.add('walk', [0,1,2,3,4,5,6,7,8,9,10,11]);
        
        //globo
        this.balloon = game.add.sprite(0, -260, 'balloon');
        this.balloon.anchor.setTo(0.5,0.5);
        this.basket = game.add.sprite(0, -150, 'balloon_basket');
        this.basket.anchor.setTo(0.5,0.5);
    },

    update: function() { 
        if(this.kid.y>=460){
            this.kid.animations.play('walk', 6, true);
            if(this.kid.x<=700){
                this.kid.x += 2;
            }else{
                if(oneMenu){
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
        }else{
            this.balloon.y += 2;
            this.basket.y += 2;
            this.kid.y +=2;
            this.balloon.x += 1;
            this.basket.x += 1;
            this.kid.x +=1;
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
