<!DOCTYPE html>  
<html>
    <head>  
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title> Fractions </title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <script type="text/javascript" src="js/phaser.min.js"></script>
        <script type="text/javascript" src="js/boot.js"></script>
        <script type="text/javascript" src="js/menu.js"></script>
        <script type="text/javascript" src="js/circleOne.js"></script>
        <script type="text/javascript" src="js/squareOne.js"></script>
        <script type="text/javascript" src="js/squareTwo.js"></script>
    </head>

    <body>

    <div class="container">
    	<div class="clearfix"></div>
        <div class="panel panel-primary">
          <div class="panel-heading">FRACTIONS GAME</div>
          <div class="panel-body">
            <center>
                <div id="fractions-game" style="padding: 0 auto 0 auto;"></div>
            </center>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">COOPERATION TEAM</div>
          <div class="panel-body">
            <center>
                <ul>
                  <li><strong>BRAZIL:</strong> Le&ocirc;nidas de Oliveira Brand&atilde;o (IME-USP)</li>
                  <li><strong>PERU:</strong> Manuel Ibarra and Cristhian Serrano (EAPIIS-UNAMBA)</li>
                  <li><strong>FRANCE:</strong> Jean-Marc (MOCAH-UPMC)</li>
                </ul>
            </center>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">BASED ON</div>
          <div class="panel-body">
            <center>
                <ul>
                  <li><strong>iLM </strong>(interactive Learning Modules) </li>
                  <li><strong>Six facets of Serious Game Design</strong>:<br>
Pedagogical Objectives; Domain Simulation; Interactions with the Simulation; Problems and Progression; Decorum and Conditions of Use.
</li>
                </ul>
            </center>
          </div>
        </div>
        <div class="panel panel-danger">
          <div class="panel-heading">TECHNOLOGY</div>
          <div class="panel-body">
            <center>
                <ul>
                  <li> We used <strong>HTML5</strong>, <strong>CSS</strong> and the <strong>Javascript</strong> Library <a href="http://phaser.io/" target="_blank"><strong>Phaser.io</strong></a> </li>
                </ul>
            </center>
          </div>
        </div>
    </div>
        
    </body>

    
    <?php /* retrieving parameters */ 
        
    $do = $_REQUEST['do'];
    $lang = $_REQUEST['language'];
    $shape = $_REQUEST['shape'];
    $type = $_REQUEST['mode'];
    $posit = 0;
    $opera = $_REQUEST['operator'];
    $diffi = $_REQUEST['difficulty'];
    $label = $_REQUEST['label'];

    //?do=play&language=es_PE&shape=Circle&mode=A&operator=Plus&difficulty=1&label=true
    
    if(!isset($do)){
    ?>
    
    <script type="text/javascript">
        // Initialize the game
        var game = new Phaser.Game(900, 600, Phaser.CANVAS, 'fractions-game');
        
        hip = "<?=$_SERVER['REMOTE_ADDR']?>"; //Host ip
        name = ""; //player name
        lang = ""; //language
        var timer, totalTime;
            // Game One 
         onePosition = 0; //Map position
         oneMove = false; //Move to next position
         oneDifficulty = 0; //From one to five 
         oneOperator= ""; //Plus; Minus; Mixed
         oneLabel= false; //Show block label
         oneShape = ""; //Circle; square
         oneType = ""; // A - Place distance; B - Select blocks
         oneMenu = true;
            // Game Two
         twoPosition = 0; //Map position
         twoMove = false; //Move to next position
         twoDifficulty = 0; //From one to five 
         twoOperator= ""; //Plus; Minus; Mixed
         twoLabel= false; //Show block label
         twoShape = ""; //Circle; square
         twoType = ""; // A - Normal position; B - Random position
         twoMenu= true;
        
        //adding game states (scenes)
        game.state.add('boot', bootState);  
        game.state.add('load', loadState); 
        game.state.add('name', nameState);
        game.state.add('menu', menuState);  
        
        game.state.add('menuCOne', menuCircleOne);
        game.state.add('mapCOne', mapCircleOne);
        game.state.add('gameCOne', gameCircleOne);
        game.state.add('endCOne', endCircleOne);
                
        game.state.add('menuSOne', menuSquareOne);
        game.state.add('mapSOne', mapSquareOne);
        game.state.add('gameSOne', gameSquareOne);
        game.state.add('endSOne', endSquareOne);
        
        game.state.add('menuSTwo', menuSquareTwo);
        game.state.add('mapSTwo', mapSquareTwo);
        game.state.add('gameSTwo', gameSquareTwo);
        game.state.add('endSTwo', endSquareTwo);
        
        //starting to boot game
        game.state.start('boot');
    </script>
    <? } else if($do=="play"){ ?>
    
    <script type="text/javascript">
        // Initialize the game
        var game = new Phaser.Game(900, 600, Phaser.CANVAS, 'fractions-game');
        
        var hip = "<?=$_SERVER['REMOTE_ADDR']?>"; //Host ip
        var name = "";
        var lang = "<?=$lang?>";
        var timer, totalTime;
        
        var onePosition = <?=$posit?>;
        var oneMove = true;
        var oneDifficulty = <?=$diffi?>;
        var oneOperator = "<?=$opera?>";
        var oneLabel = <?=$label?>;
        var oneShape = "<?=$shape?>";
        var oneType = "<?=$type?>";
        var oneMenu = false;
        
        var twoPosition = 0; //Map position
        var twoMove = false; //Move to next position
        var twoDifficulty = 0; //From one to five 
        var twoOperator= ""; //Plus; Minus; Mixed
        var twoLabel= false; //Show block label
        var twoShape = ""; //Circle; square
        var twoType = ""; // A - Normal position; B - Random position
        var twoMenu= true;
        
        //adding game states (scenes) 
        game.state.add('boot', bootState); 
        game.state.add('load', loadState); 
        game.state.add('name', nameState);
        
        game.state.add('menuCOne', menuCircleOne);
        game.state.add('mapCOne', mapCircleOne);
        game.state.add('gameCOne', gameCircleOne);
        game.state.add('endCOne', endCircleOne);
                
        game.state.add('menuSOne', menuSquareOne);
        game.state.add('mapSOne', mapSquareOne);
        game.state.add('gameSOne', gameSquareOne);
        game.state.add('endSOne', endSquareOne);
        
        game.state.add('menuSTwo', menuSquareTwo);
        game.state.add('mapSTwo', mapSquareTwo);
        game.state.add('gameSTwo', gameSquareTwo);
        game.state.add('endSTwo', endSquareTwo);
        
        //starting to load game
        game.state.start('load');
    </script>
    <? } ?>
</html>