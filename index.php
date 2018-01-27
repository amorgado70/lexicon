<?php

// Domains - Sites

// englishisfun.club
// englishisfunclub.alvm.dom...	Endereço de testes.
// whw0059.sslblindado.com/e...	Endereço SSL.

// Connect to FTP

// Usuário FTP: alvm - definir senha
// Endereço FTP: ftp.alvm.com.br ou ftp.whw0059.whservidor.com

// Connect to database

$servername = "dictionary.mysql.uhserver.com";
$username = "amorgado";
$password = "@morgado2015";
$database = "dictionary";

    $oDb = new PDO("mysql:host=$servername;dbname=$database", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    // set the PDO error mode to exception
    $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Use Slim Framework 2.6

//require "vendor/autoload.php";
//require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/vendor/autoload.php";

$oApp = new \Slim\Slim(array(
        'view' => new \PHPView\PHPView(),
        //'templates.path' => __DIR__ . "/../views" ));
		'templates.path' => __DIR__ . "/views" ));

// Basic Routes

$oApp->get("/", function() {
    renderCategory();
});

$oApp->get("/uploadPicture", function()use($oApp){
   $oApp->render("uploadPicture.phtml");
});

$oApp->get("/words/:nID/:sIDs", function($nID,$sIDs){
    renderWord($nID,$sIDs);
});

// Words CRUD

$oApp->get("/crudWord", function()use($oApp){
   $oApp->render("crudWord.phtml");
});

$oApp->get("/crudWord/words", new \Auth(), function() use($oDb, $oApp){
    $oStmt = $oDb->prepare("SELECT * FROM words");
    $oStmt->execute();
    $aWords = $oStmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($aWords);
});

$oApp->get("/crudWord/wordsCategory/:categoryID", new \Auth(), function($nCategoryID) use($oDb, $oApp){
    $oStmt = $oDb->prepare("SELECT * FROM words WHERE categoryID = :categoryID");
    $oStmt->bindParam("categoryID", $nCategoryID);
    $oStmt->execute();
    $aWords = $oStmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($aWords);
});

$oApp->post("/crudWord/words", new \Auth(), function() use($oDb, $oApp){
    $oData = json_decode($oApp->request->getBody());
    $oStmt = $oDb->prepare("INSERT INTO words(ID, word, imageURL, definition, example, pronunciation, categoryID, levelID, otherForms) VALUES(:ID, :word, :imageURL, :definition, :example, :pronunciation, :categoryID, :levelID, :otherForms)");
    $oStmt->bindParam("ID", $oData->ID);
    $oStmt->bindParam("word", $oData->word);
    $oStmt->bindParam("imageURL", $oData->imageURL);
	$oStmt->bindParam("definition", $oData->definition);
	$oStmt->bindParam("example", $oData->example);
	$oStmt->bindParam("pronunciation", $oData->pronunciation);
	$oStmt->bindParam("categoryID", $oData->categoryID);
	$oStmt->bindParam("levelID", $oData->levelID);
	$oStmt->bindParam("otherForms", $oData->otherForms);
    $oStmt->execute();
    echo json_encode($oData);
});

$oApp->post("/crudWord/words/:wordID", new \Auth(), function($nWordID) use($oDb, $oApp){
    $oData = json_decode($oApp->request->getBody());
    $oStmt = $oDb->prepare("UPDATE words SET word = :word, imageURL = :imageURL, definition = :definition, example = :example, pronunciation = :pronunciation, categoryID = :categoryID, levelID = :levelID, otherForms = :otherForms WHERE ID = :id");
    $oStmt->bindParam("word", $oData->word);
    $oStmt->bindParam("imageURL", $oData->imageURL);
	$oStmt->bindParam("definition", $oData->definition);
	$oStmt->bindParam("example", $oData->example);
	$oStmt->bindParam("pronunciation", $oData->pronunciation);
	$oStmt->bindParam("categoryID", $oData->categoryID);
	$oStmt->bindParam("levelID", $oData->levelID);
	$oStmt->bindParam("otherForms", $oData->otherForms);
    $oStmt->bindParam("id", $nWordID);
    $oStmt->execute();
    echo json_encode($oData);
});

$oApp->delete("/crudWord/words/:wordID", new \Auth(), function($nWordID) use($oDb, $oApp){
    $oStmt = $oDb->prepare("DELETE FROM words WHERE ID = :id");
    $oStmt->bindParam("id", $nWordID);
    $oStmt->execute();
    echo '{"result":"success"}';
});

// Categories CRUD

$oApp->get("/crudCategory", function()use($oApp){
   $oApp->render("crudCategory.phtml");
});

$oApp->get("/crudCategory/categories", new \Auth(), function() use($oDb, $oApp){
    $oStmt = $oDb->prepare("SELECT * FROM categories");
    $oStmt->execute();
    $aCategories = $oStmt->fetchAll(PDO::FETCH_OBJ);
    echo json_encode($aCategories);
});

$oApp->post("/crudCategory/categories", new \Auth(), function() use($oDb, $oApp){
    $oData = json_decode($oApp->request->getBody());
    $oStmt = $oDb->prepare("INSERT INTO categories(ID, name, image, reference) VALUES(:ID, :name, :image, :reference)");
    $oStmt->bindParam("ID", $oData->ID);
    $oStmt->bindParam("name", $oData->name);
    $oStmt->bindParam("image", $oData->image);
	$oStmt->bindParam("reference", $oData->reference);
    $oStmt->execute();
    echo json_encode($oData);
});

$oApp->post("/crudCategory/categories/:categoryID", new \Auth(), function($nCategoryID) use($oDb, $oApp){
    $oData = json_decode($oApp->request->getBody());
    $oStmt = $oDb->prepare("UPDATE categories SET name = :name, image = :image, reference = :reference WHERE ID = :id");
    $oStmt->bindParam("name", $oData->name);
    $oStmt->bindParam("image", $oData->image);
	$oStmt->bindParam("reference", $oData->reference);
    $oStmt->bindParam("id", $nCategoryID);
    $oStmt->execute();
    echo json_encode($oData);
});

$oApp->delete("/crudCategory/categories/:categoryID", new \Auth(), function($nCategoryID) use($oDb, $oApp){
    $oStmt = $oDb->prepare("DELETE FROM categories WHERE ID = :id");
    $oStmt->bindParam("id", $nCategoryID);
    $oStmt->execute();
    echo '{"result":"success"}';
});

// Authentication

$oApp->get("/login", function() use( $oApp){
    // see if this is the original redirect or if it's the callback
    $sCode = $oApp->request->params('code');
    // get the uri to redirect to
    $sUrl = "http";
    if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
    {
        $sUrl .= "s";
    }
    $sUrl .= "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $oAuth = new \Oauth2($sUrl);
    if($sCode == null){
        $oApp->response->redirect($oAuth->redirectUrl());
    }else{
        $oAuth->handleCode($sCode);
        $oApp->response->redirect("/crudWord");
    }
});
$oApp->get("/currentUser", new \Auth(), function() use($oApp){
    echo json_encode($_SESSION['CurrentUser']);
});
$oApp->get("/logout", function(){
    session_start();
    unset($_SESSION["CurrentUser"]);
    renderCategory();
});

// ................................

$oApp->run();

function renderCategory(){
    global $oApp, $oDb;
    //fetching list of categories
    $oStmt = $oDb->prepare("SELECT * FROM categories");
    $oStmt->execute();
    $aCategories = $oStmt->fetchAll(PDO::FETCH_OBJ);

    //fetching list of words
    $oStmt = $oDb->prepare("SELECT ID, categoryID FROM words");
    $oStmt->execute();
    $aWords = $oStmt->fetchAll(PDO::FETCH_OBJ);

    // render template with data
    $oApp->render("category.phtml", array("words"=>$aWords,"categories"=>$aCategories));
}

function renderWord($nID,$sIDs){
    global $oApp, $oDb;
    // fetching word
    $oStmt = $oDb->prepare("SELECT * FROM words WHERE ID = :id");
    $oStmt->bindParam("id", $nID);
    $oStmt->execute();
    $aWord = $oStmt->fetchAll(PDO::FETCH_OBJ);

    // render template with data
    $oApp->render("word.phtml", array("word"=>$aWord[0],"wordIDs"=>$sIDs));
}

?>
