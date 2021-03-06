<div class="mes-factures">
    <h2>Mes Factures</h2>

    <?php if (empty($factures)): ?>
        <p>Vous ne disposez actuellement d'aucune facture.</p>
    <?php else: ?>
        <?php
        $years = array();
        foreach ($factures as $facture) {
            $years[] = $facture->year;
        }
        $years = array_unique($years);
        rsort($years);
        ?>
        <p>Consulter les factures de l’année :</p>
        <select name="" class="js-account-filter-by-year" id="">
            <?php foreach ($years as $year): ?>
                <option value="<?php echo $year ?>" <?php echo $years[0] === $year ? 'selected' : '' ?>><?php echo $year ?></option>
            <?php endforeach; ?>
        </select>
        <ul class="liste-factures">
        <?php foreach($factures as $facture): ?>
            <li class="js-account-filter-facture-<?php echo $facture->year ?> <?php echo $years[0] !== $facture->year ? 'hidden' : '' ?>">
                <ul>
                    <?php
                    $options = array(
                        'controller' => 'documents',
                        'action'     => 'download',
                        'id'         => $facture->id
                    );
                    $publicUrl  = MvcRouter::public_url($options);
                    ?>
                    <li>
                        <a href="<?php echo $publicUrl?>" class="num">
                            N° <?php echo $facture->numero_facture ?>
                        </a>
                    </li>
                    <li>
                        <div class="date">
                            Du <?php echo $facture->day.'/'.$facture->month.'/'.$facture->year ?>
                        </div>
                    </li>
                    <li>
                        <div class="libele">
                            <?php echo !empty(Config::$libellesFactures[strtoupper($facture->type_facture)]) ? Config::$libellesFactures[strtoupper($facture->type_facture)] : $facture->type_facture ?>
                        </div>
                    </li>
                    <li>
                        <a href="<?php echo $publicUrl?>" target="_blank" class="pdf"></a>
                    </li>
                </ul>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>