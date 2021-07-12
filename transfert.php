<?php

namespace transfert {


    $lesfilms = simplexml_load_file('../allocineseances.xml');
    foreach ($lesfilms->xpath('//film') as $filmxml) {

        echo $filmxml['titre'] . '-<br />';
        foreach ($filmxml->horaire as $horaire) {
            echo $horaire['version']   . '-<br />';
            $seance = explode(";", $horaire);
            for ($line = 0; $line < count($seance); $line++) {
                echo $seance[$line]   . '+<br />';
            }
        }
    }
}
