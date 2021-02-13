
/*
    var mapState = {
        create: function(){},
        update: function(){},
        ---------------------------- end of phaser functions
        func_loadGame: function(){},
    }
*/

var mapState = {

    create: function() {

        if(levelType=="C"){
            this.gameStateString = "game"+levelShape+"Two";
            this.endStateString = "end"+levelShape+"Two";
            this.menuStateString = "menu"+levelShape+"Two";
        }else{
            this.gameStateString = "game"+levelShape+"One";
            this.endStateString = "end"+levelShape+"One";
            this.menuStateString = "menu"+levelShape+"One";
        }
        
        // Background
        game.add.image(0, 40, 'bgmap');
        
        // Navigation buttons
        buttonSettings["func_addButtons"](true,false,
                                    true,true,false,
                                    false,false,
                                    this.menuStateString,false);
        
        // Styles for labels
        var stylePlace = { font: '26px Arial', fill: '#ffffff', align: 'center'};
        var styleMenu = { font: '30px Arial', fill: '#000000', align: 'center'};
        
        // Progress bar
        var percentText = passedLevels*25;
        var percentBlocks = passedLevels;

        for(var p=0;p<percentBlocks;p++){
            var block = game.add.image(660+p*37.5, 10, 'block');
                block.scale.setTo(2.6, 1);
        }

        game.add.text(820, 10, percentText+'%', styleMenu);
        game.add.text(650, 10, lang.difficulty + ' ' + levelDifficulty, styleMenu).anchor.setTo(1,0);
        game.add.image(660, 10, 'pgbar');
        
         //Road
        this.points = {
            'x': [ 90, 204, 318, 432, 546, 660 ],
            'y': [ 486, 422, 358, 294, 230, 166 ]
        };
        
        if(this.gameStateString=="gameSquareOne"){
        	//Garage
	        var garage = game.add.image(this.points.x[0], this.points.y[0], 'garage');
	        garage.scale.setTo(0.4);
	        garage.anchor.setTo(0.5, 1);
	         //Farm
	        var farm = game.add.image(this.points.x[5], this.points.y[5], 'farm');
	        farm.scale.setTo(0.6);
	        farm.anchor.setTo(0.1, 0.7);
        }else{
	      	//House
	        var house = game.add.image(this.points.x[0], this.points.y[0], 'house');
	        house.scale.setTo(0.7);
	        house.anchor.setTo(0.7, 0.8);
	         //School
	        var school = game.add.image(this.points.x[5], this.points.y[5], 'school');
	        school.scale.setTo(0.35);
	        school.anchor.setTo(0.2, 0.7);
	    }

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
            if(p<levelPosition){
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_b');
            }else if (levelMove && p==levelPosition){
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_b');
            }else{
                place = game.add.image(this.points.x[p], this.points.y[p], 'place_a');
            }
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

        if(this.gameStateString=="gameSquareOne"){
	    	this.character = game.add.sprite(this.points.x[levelPosition], this.points.y[levelPosition], 'tractor');

	        var walk = this.character.animations.add('walk',[0,1,2,3,4]);
	        this.character.animations.play('walk', 5, true);
	        this.character.angle -= 25;
        }else{
	        this.character = game.add.sprite(this.points.x[levelPosition], this.points.y[levelPosition], 'kid_run');

	        this.character.animations.add('run');
	        this.character.animations.play('run', 6, true);
        }
        this.character.anchor.setTo(0.5, 1);
        this.character.scale.setTo(0.5);
        game.physics.arcade.enable(this.character);

        // Delay to next level
        this.count = 0;
        this.wait = 60;
        
    },

    update: function() {
        
        // Wait 2 seconds before moving or staring a game
        this.count ++;
        if(this.count<=this.wait) return;
        
        // If movement is stopped or position is 6 (final), load game
    	if(this.gameStateString=="gameSquareOne"){
		    if(levelPosition==8){
	            levelMove = false;
	        }
		}else if(this.gameStateString=="gameCircleOne"){
			if(levelPosition==6){
	            levelMove = false;
	        }
		}else if(this.gameStateString=="gameSquareTwo"){
			if(levelPosition==5){
	            levelMove = false;
	        }
		}


        if(!levelMove){
            this.func_loadGame();
        }
        
        // If momevent is enabled, move to next point from actual
        if(levelMove){
            game.physics.arcade.moveToXY(
                this.character, 
                this.points.x[levelPosition+1],
                this.points.y[levelPosition+1],
                100
            );    	
            
            // I kid/tractor reached the end, stop movement
            if(Math.ceil(this.character.x)==this.points.x[levelPosition+1] || Math.ceil(this.character.y)==this.points.y[levelPosition+1]){
                levelMove=false;
                levelPosition += 1; //Update position
            }
        }
    },
        
    //MapLoading function
    func_loadGame: function(){
    	
        if(audioStatus){
            beepSound.play();
        }

        if(levelPosition<5){
        	game.state.start(this.gameStateString);
        }else{
        	game.state.start(this.endStateString);
    	}

    }
    
};