
<?php
    function anagramme(string $mot1, string $mot2):bool{
        $mot1=strtolower($mot1);
        $mot2=strtolower($mot2);

        if(strlen($mot1)!=strlen($mot2)){
            return false;
        }
        $compteur1=array_count_values(str_split($mot1));
        $compteur2=array_count_values(str_split($mot2));

        foreach($compteur1 as $k =>$o){
            if($compteur1[$k]!==$compteur2[$k]){
                return false;
            }
            
        }
        return true;
    }


    echo anagramme("chien","niche")? "True":"False";
?>
