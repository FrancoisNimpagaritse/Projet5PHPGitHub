<?php

abstract class Controller
{
    //Load model and get data from it
    public function loadModel(string $model)
    {
        require_once(URLROOT.'models/'.$model.'.php');
        
        //Instantiate model class
        return new $model();       
    }

    //Load view and pass data from controller to it
    public function loadView(string $view, array $data = [])
    {
        extract($data);
        if(file_exists(URLROOT.'views/'.$view.'View.php'))
        {
            require_once(URLROOT.'views/'.$view.'View.php');
        }
        else
        {
            http_response_code(404);
            echo "<h1>Erreur 404</h1>";
            echo "Page introuvable!"; 
        }
    }

    //Load view and pass data from controller to it
    public function loadAdminView(string $view, array $data = [])
    {
        extract($data);
        if(file_exists(URLROOT.'views/admin/'.$view.'View.php'))
        {
            require_once(URLROOT.'views/admin/'.$view.'View.php');
        }
        else
        {
            http_response_code(404);
            echo "<h1>Erreur 404</h1>";
            echo "Page introuvable!"; 
        }
    }
}