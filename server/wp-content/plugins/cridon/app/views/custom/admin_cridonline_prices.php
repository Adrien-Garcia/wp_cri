<form method="post" action="options.php" onsubmit="return confirm('Êtes-vous sûr de vouloir sauvegarder les prix cridonline ?');">
    <?php wp_nonce_field('update-options') ?>
    </br>
    <?php if($_GET['settings-updated']) {echo '<p>Les prix ont été correctement sauvegardés en base</p>';} ?>
    </br>
    <!-- On ajoute le niveua 1 à 0€ pour que ce soit pratique de manipuler ces données dans le controlleur -->
    <input type="hidden" name="cridonline_prices_year_N[1][1]" value="0">
    <input type="hidden" name="cridonline_prices_year_N[1][2]" value="0">
    <input type="hidden" name="cridonline_prices_year_N[1][5]" value="0">
    <input type="hidden" name="cridonline_prices_year_N_plus_1[1][1]" value="0">
    <input type="hidden" name="cridonline_prices_year_N_plus_1[1][2]" value="0">
    <input type="hidden" name="cridonline_prices_year_N_plus_1[1][5]" value="0">

    <table border="1" cellpadding="0" cellspacing="0" >
        <col>
        <colgroup span="2"></colgroup>
        <colgroup span="2"></colgroup>
        <tr>
            <td rowspan="2"></td>
            <th colspan="2" scope="colgroup">Prix année <?php echo date('Y') ?></th>
            <th colspan="2" scope="colgroup">Prix année <?php echo date('Y', strtotime('+1 year')) ?></th>
        </tr>
        <tr>
            <th scope="col">Niveau 2</th>
            <th scope="col">Niveau 3</th>
            <th scope="col">Niveau 2</th>
            <th scope="col">Niveau 3</th>
        </tr>
        <tr>
            <th scope="row">1 Notaire</th>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[2][1]" value="<?php echo !empty($year_N[2][1]) ? $year_N[2][1] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[3][1]" value="<?php echo !empty($year_N[3][1]) ? $year_N[3][1] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[2][1]" value="<?php echo !empty($year_N_plus_1[2][1]) ? $year_N_plus_1[2][1] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[3][1]" value="<?php echo !empty($year_N_plus_1[3][1]) ? $year_N_plus_1[3][1] : 0 ?>" /></td>
        </tr>
        <tr>
            <th scope="row">2-4 Notaires</th>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[2][2]" value="<?php echo !empty($year_N[2][2]) ? $year_N[2][2] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[3][2]" value="<?php echo !empty($year_N[3][2]) ? $year_N[3][2] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[2][2]" value="<?php echo !empty($year_N_plus_1[2][2]) ? $year_N_plus_1[2][2] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[3][2]" value="<?php echo !empty($year_N_plus_1[3][2]) ? $year_N_plus_1[3][2] : 0 ?>" /></td>
        </tr>
        <tr>
            <th scope="row">5+ Notaires</th>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[2][5]" value="<?php echo !empty($year_N[2][5]) ? $year_N[2][5] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N[3][5]" value="<?php echo !empty($year_N[3][5]) ? $year_N[3][5] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[2][5]" value="<?php echo !empty($year_N_plus_1[2][5]) ? $year_N_plus_1[2][5] : 0 ?>" /></td>
            <td><input type="number" step="any" min="0" name="cridonline_prices_year_N_plus_1[3][5]" value="<?php echo !empty($year_N_plus_1[3][5]) ? $year_N_plus_1[3][5] : 0 ?>" /></td>
        </tr>
    </table>
    <?php echo submit_button() ?>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="cridonline_prices_year_N,cridonline_prices_year_N_plus_1" />
</form>
