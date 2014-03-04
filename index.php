<?php
    define("WEEK_START_NB", 33);
    define("NUMBER_WEEK_IN_YEAR", 52); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    
    <style>
        body{
            font-family: Arial, Tahoma, Verdana, sans-serif;
            font-size: 12px;
            font-weight: bold;
            margin: 0;
        }
        
        #info {
            text-align: center;
            background-color: #D3D3D3;
            border: 1px solid black;
            margin-top: -1px;
            width: 100%;
        }
        
        #jour {
            width: 100px;
        }
        
        #semaine {
            width: 80px;
        }
        
        label {
            margin-left: 20px;
            margin-right: 10px;
        }
        
        .error {
            margin-top: 10px;
            text-align: center;
            color: red;
            font-weight: bold;
        }
        
        img.pacman {
            margin-top: 100px;
            margin-bottom: 100px;
        }
    </style>
    
    <title>Emploi du temps Université Jean-Monnet (Saint-Etienne)</title>
</head>
<body style="text-align: center;">

<?php
    // Récupération du code du lien sans quoi on ne pourrait pas voir l'image de l'edt
    //exec("./code_edt.pl", $output);
    //$contenu = $output[0];
    
    //if(!$contenu)
    //    echo "<p cpass='error'>Problème avec la récupération de la clé</p>";
    
    //$code = $contenu;
?>
    
    <label for="code_promo">Promo :</label>
    <select id="code_promo">
        <option value='3215'>L1 SVTE</option>
        <option value='5844'>L1 Staps</option>
        <option value='3797'>L1 Sc. & Techno</option>
        <option value='3243%2C3244%2C3245%2C3246'>L2 Bio</option>
        <option value='3235'>L2 Info</option>
        <option value='3231'>L2 Math</option>
        <option value='5788'>L2 Sc. Terre & Envi</option>
        <option value='6677'>L2 Staps</option>
        <option value='5790'>L3 Sc. Ingé</option>
        <option value='3277'>L3 Bio</option>
        <option value='3275'>L3 Chimie</option>
        <option value='3083' selected="selected">L3 Info</option>
        <option value='4760'>L3 Mass</option>
        <option value='4762'>L3 Math</option>
        <option value='3274'>L3 Physique</option>
        <option value='2562'>L3 Phy. & Chimi</option>
        <option value='4361'>L3 SVTE</option>
        <option value='3293'>M1 Info</option>
        <option value='3284'>M1 Math</option>
    </select>
    
    <label for="jour">Jours :</label>
<?php

    /* Les jours de la semaine */
    $array = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    $day = date("w") - 1; /* -1 Car on veut pas dimanche ( dimanche = 0 ) Du coup la Lundi = 0 [ pratique pour le tableau au dessus ] */
    
    echo '<select id="jour" size="6" multiple="multiple">';
    
    /* Pour tout les jours de la semaine de 0 a 5 car le dimanche n'est pas dispo sur l'emploi du temps */
    for($i = 0; $i <= 5; $i++) {
        echo "<option value='$i'";
    
        if(($day == -1 && $i == 0) || $i == $day) /* Si on est dimanche on le met a lundi, sinon on sélectionne le jour actuel */
            echo ' selected="selected"'; 
            
        echo ">";
        
        echo $array[$i] . "</option>";
    }
    
    echo '</select>';
?>
    
    <label for="semaine">Semaine :</label>
    <input type="button" id="semaine_precedente" value="<" />
    
    <select id="semaine">
    <?php
    
        // Retourne la date correspondant au lundi de de la semaine $semaine et de l'année $annee
        function premierlundi($semaine, $annee) {
            if($semaine <= WEEK_START_NB)
                $annee++;
                
            // Timestamp de l'année jours 1 - mois 1
            $begin = mktime(0, 0, 0, 1, 1, $annee);
            
            // On décale de $semaine par rapport au debut de l'année
            $offset = strtotime("+$semaine weeks", $begin);
        
            if (date('w', $offset) != 1) // Si c'est un lundi , on a gagné :p
                $offset = strtotime("last monday", $offset); // On utilise la puissance du php et on se deplace au lundi
        
            return date('d-m', $offset);
        }
    
        /* Pourquoi -1 , car l'année débute scolaire commence au milieu de l'année [ ex : Debut aout 2013 ]
         * Du coup les semaines sont comptées à partir de la première date du début des cours
         * Ex : Debut de l'emploi du temps la semaine 33 donc la semaine 33 [ semaine réelle ] est égale a 0
         * Sur l'emploi du temps, on décale alors tout depuis cette date
         */
        $date = date("Y") - 1;
        $num_semaine = strftime("%U");
        
        for ($i = 0 ; $i <= NUMBER_WEEK_IN_YEAR ; $i++) {
            $val = WEEK_START_NB + $i;
            
            if( $val > NUMBER_WEEK_IN_YEAR )
                $val = $i - (NUMBER_WEEK_IN_YEAR - WEEK_START_NB);
                
            if($num_semaine + ( NUMBER_WEEK_IN_YEAR - WEEK_START_NB) == $i) {
                $sv_num_semaine = $i;
                echo "<option value='$i' selected='selected'>";
            }
            else {
                echo "<option value='$i'>";
            }
        
            echo premierlundi($val, $date);
            
            echo "</option>";
        }
    ?>
    
    </select>
    
    <input type="button" id="semaine_suivante" value="&gt;" />
    
    <br /><br />
    
    <img alt="Emploi du temps" id="image" class="pacman" src="img/pacman.gif">
    
    <p>Code grandement inspiré de <a href="https://github.com/kaldoran/web/blob/master/ujm_emploi_du_temps.php" title="merci">Nicolas Reynaud</a></p>

    
    <script>
        var code;
        
        function request(callback) {
            var xhr = new XMLHttpRequest();
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                    callback(xhr.responseText);
                }
            };
            
            xhr.open("GET", "code_edt.cgi", true);
            xhr.send(null);
        }
        
        function readData(data) {
           /<p>(.*?)<\/p>/.exec(data);
           code = RegExp.$1;
           
           var img = document.getElementById('image');
           img.className = img.className.replace("pacman", "");
           
           goto(0);
        }
        
        request(readData);
        
        function goto(val) {
            var semaine_obj = document.getElementById('semaine');
            var code_promo_obj = document.getElementById('code_promo');
            var jour_obj = document.getElementById('jour');
 
            var code_promo = code_promo_obj.options[code_promo_obj.selectedIndex].value;
            var semaine = semaine_obj.options[semaine_obj.selectedIndex].value;
            
            semaine = parseInt(semaine) + parseInt(val);
    
            semaine_obj.options[semaine].selected = true;
            
            var tab_jours = new Array;
            var jours_options = jour_obj.options;
            
            // Récupération de tous les jours sélectionnés
            for ( var i=0; i< jours_options.length; i++) {
                if ( jours_options[i].selected == true ) 
                    tab_jours.push(jours_options[i].value);
            }
            
            var lien = "http://planning.univ-st-etienne.fr/ade/imageEt?identifier="+code+"&projectId=7&idPianoWeek="+semaine+"&idPianoDay="+tab_jours+"&idTree="+code_promo+"&width=1300&height=500&lunchName=REPAS&displayMode=1057855&showLoad=false&ttl=1378793160704&displayConfId=56";
            document.getElementById('image').src = lien;
        }
        
        var code_promo = document.getElementById('code_promo');
        code_promo.addEventListener("change", function() { goto(0) }, false);
        
        var jour = document.getElementById('jour');
        jour.addEventListener("change", function() { goto(0) }, false);
        
        var semaine = document.getElementById('semaine');
        semaine.addEventListener("change", function() { goto(0) }, false);
        
        var semaine_precedente = document.getElementById('semaine_precedente');
        semaine_precedente.addEventListener("click", function() { goto(-1) }, false);

        var semaine_suivante = document.getElementById('semaine_suivante');
        semaine_suivante.addEventListener("click", function() { goto(1) }, false);
        
        
        

        
    </script>
    
</body>
</html>
