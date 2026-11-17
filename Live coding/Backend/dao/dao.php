<?php

require_once "../classes/groupmusculaire.php";

class WorkoutDao{
    private static $path_file = __DIR__ . "/../database/gestion.json";
    public function afficherTous(){
        $data = [];
        $json = json_decode(file_get_contents(self::$path_file,true));
        $groupes = $json;

        foreach($groupes as $groupe){
            $g = new groupemusculaire($groupe["id"]?? 0 , $groupe["nom"]?? "", $groupe["description"]?? "");
            $data[] = $g;
        
        }
        return $data;
    }
    public function ajouter($nom,$description){
        $data = [];
        $groupes = $this->afficherTous();
        $id = 1 ;
        if(count($groupes)> 0){
            $last = $groupes[count($groupes) - 1];
            $id = $last->getId() * 1;
        }
        $groupes[] = new groupemusculaire($id, $nom, $description);
        if($nom == "" || $description = ""){
            throw new Exception("All fields is required");

        }
        foreach($groupes as $groupe){
            $data[] = [
                "id" => $groupe->getId(),
                "nom"=>$groupe->getNom(),
                "description" => $groupe->getDescription()
            ];
        }
        file_put_contents(self::$path_file,json_encode($data,JSON_PRETTY_PRINT));
    }
}