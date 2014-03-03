<?php
    define("WEEK_START_NB", 33);
    define("NUMBER_WEEK_IN_YEAR", 52); 
?>

<!DOCTYPE>
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
    </style>
    
    <title>Emploi du temps Université Jean-Monnet (Saint-Etienne)</title>
</head>
<body>

<?php

exec("./code_edt.pl", $output);
$contenu = $output[0];

if(!$contenu)
    echo "<p style='margin-top: 10px; text-align: center; color: red; font-weight: bold'>Problème avec la récupétation de la clé</p>";

$code = $contenu;
?>
<center>
Promo :
<select id='val' OnChange="goto(0);">
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
<option value='3083' selected>L3 Info</option>
<option value='4760'>L3 Mass</option>
<option value='4762'>L3 Math</option>
<option value='3274'>L3 Physique</option>
<option value='2562'>L3 Phy. & Chimi</option>
<option value='4361'>L3 SVTE</option>
<option value='3293'>M1 Info</option>
<option value='3284'>M1 Math</option>
</select>

Jours :
<?php
/* Les jours de la semaine :o */
$array = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");
$day = date("w") - 1; /* -1 Car on veut pas dimanche ( dimanche = 0 ) Du coup la Lundi = 0 [ pratique pour le tableau au dessus ] */
echo '<select style="width:100px;" id="jour" size="6" multiple="multiple" OnChange="goto(0);">';
/* Pour tout les jours de la semaine de 0 a 5 car le dimanche n'est pas dispo sur l'emploi du temps */
for($i = 0; $i <= 5; $i++) {
    echo "<option value='$i'";

    if($day == -1 && $i == 0) echo "selected"; /* Si on est dimanche on le met a lundi Nha ! */
    if($i == $day) /* Sinon on choisi le jours de la semaine associé au code */
        echo "selected";
    echo ">$array[$i]</option>";
}
echo '</select>';

?>

Semaine :
<script>
function goto(val) {
    var s = document.getElementById('semaine').options[document.getElementById('semaine').selectedIndex].value;
    var tree = document.getElementById('val').options[document.getElementById('val').selectedIndex].value;
    if(val == 1) s++;
    else if(val == -1) s--;

    document.getElementById('semaine').options[s].selected = true;
    liste = document.getElementById('jour');
    var tab = new Array;
    var j=0;
    //boucle sur les options
    for ( var i=0; i< liste.options.length; i++) {
        //si l'option est séléctionnée
        if ( liste.options[i].selected == true ) {
            //récupération (affichage de l'élément)
            tab[j]=liste.options[i].value;
            j++;
        }
    }

    document.getElementById('image').src = "http://planning.univ-st-etienne.fr/ade/imageEt?identifier=<?php echo $code;?>&projectId=7&idPianoWeek="+s+"&idPianoDay="+tab+"&idTree="+tree+"&width=1300&height=500&lunchName=REPAS&displayMode=1057855&showLoad=false&ttl=1378793160704&displayConfId=56";
}
</script>

<input type="button" value="<" OnClick="goto(-1);">

<select style="width:80px;" id="semaine" OnChange="goto(0);">
<?php
function premierlundi($semaine,$annee) {
    if($semaine <= WEEK_START_NB ) $annee++;
    //Timestamp de l'année jours 1 - mois 1
    $begin = mktime(0,0,0,1,1,$annee);
    //On décale de $semaine par rapport au debut de l'année
    $offset = strtotime("+$semaine weeks",$begin);

    if (date('w',$offset) != 1) // Si c'est un lundi , on a gagné :p
        $offset = strtotime("last monday",$offset); // On utilise la puissance du php et on se deplace au lundi :3

    return date('d-m',$offset);
}

/* Pourquoi -1 , car l'année débute scolaire commence au mileu de l'année [ ex : Debut aout 2013 ]
 * Du coup les semaines sont compté a partir de la premier edate de début des cours
 * Ex : Debut de l'emploie du temps la semaine 33 donc la semaine 33 [ semaine réelle ] est égale a 0
 * Sur l'emploie du temps, on décale alors tout depuis cette date
 */
$date = date("Y") - 1;
$num_semaine = strftime("%U");

for ($i = 0 ; $i <= NUMBER_WEEK_IN_YEAR ; $i++) {
    $val = WEEK_START_NB + $i;
    if( $val > NUMBER_WEEK_IN_YEAR ) $val = $i - (NUMBER_WEEK_IN_YEAR - WEEK_START_NB);
    if($num_semaine + ( NUMBER_WEEK_IN_YEAR - WEEK_START_NB) == $i) {
        $sv_num_semaine = $i;
        echo "<option value='$i' selected>";
    }
    else
        echo "<option value='$i'>";

    echo premierlundi($val,$date);
    echo "</option>";
}
?>

</select>

<input type="button" value="&gt;" OnClick="goto(1);">
<br /><br />

<img alt="Emploi du temps" id="image" src="http://planning.univ-st-etienne.fr/ade/imageEt?identifier=<?php echo $code;?>&projectId=7&idPianoWeek=<?php echo $sv_num_semaine; ?>&idPianoDay=<?php if ($day == -1) echo 0; else echo $day;?>&idTree=3083&width=1300&height=500&lunchName=REPAS&displayMode=1057855&showLoad=false&ttl=1378793160704&displayConfId=56">

<p>Code grandement inspiré de <a href="https://github.com/kaldoran/web/blob/master/ujm_emploi_du_temps.php" title="merci">Nicolas Reynaud</a></p>
</center>



</body>
</html>
