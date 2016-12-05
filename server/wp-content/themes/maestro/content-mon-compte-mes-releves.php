<div id="releves-conso">
    <h2>Mes Relevés de consommation</h2>
    <?php if (empty($releves)): ?>
        <p>Vous ne disposez actuellement d'aucun relevé de consommation.</p>
    <?php else: ?>
        <?php $sameYear = ''; ?>
        <?php foreach ($releves as $releve): ?>
            <?php if ($sameYear !== $releve->year): ?>
                <?php echo !empty($sameYear) ? '</ul>' : '' ?>
                <?php $sameYear = $releve->year ?>
                <div class="titre"><?php echo $releve->year ?></div>
                <ul>
            <?php endif; ?>
            <?php
            $options = array(
                'controller' => 'documents',
                'action'     => 'download',
                'id'         => $releve->id
            );
            $publicUrl  = MvcRouter::public_url($options);
            ?>
                <li>
                    <?php if ($releve->month == '12'): /* Relevé complet sur l'année */ ?>
                        <?php $text = 'Année complète'; ?>
                    <?php else: ?>
                        <?php $dateObj = DateTime::createFromFormat('!m', $releve->month, new DateTimeZone('Europe/Paris'));
                              $text    = ucfirst(strftime('%B', $dateObj->getTimestamp()));
                        ?>
                    <?php endif; ?>
                    <a href="<?php echo $publicUrl?>" target="_blank" class="mois"><?php echo $text ?></a>
                    <a href="<?php echo $publicUrl?>" target="_blank" class="pdf"></a>
                </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
