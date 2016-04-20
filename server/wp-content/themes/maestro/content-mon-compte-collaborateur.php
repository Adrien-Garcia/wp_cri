

<h2><?php _e("Ma liste de collaborateur"); ?></h2>

<div class="bt-ajout"><?php _e("Ajouter un collaborateur"); ?></div>


<ul class="list-collab">
    <?php
        foreach ($liste as $key => $member) :

            ?>
       
            <li>
                <div class="trash">
                    <form accept-charset="utf-8" class="js-account-collaborateur-delete-form" data-js-ajax-delete-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentdeletecollaborateur'));?>">
                        <input type="hidden" value="<?php echo $member->id; ?>" class="js-account-collaborateur-delete-id">
                        <input type="submit" value="submit">
                    </form>
                </div>
                <div class="block_01">
                    <span class="nom"><?php echo $member->last_name ?> <?php echo $member->first_name ?></span>                
                    <span class="fonction"><?php echo $member->id_fonction ?></span>     
                </div>
                <div class="block_02">
                    <?php echo $member->email_adress ?> 
                </div>
                <div class="block_03">
                    <?php if (!empty($member->tel)): ?>
                        <span class="tel"><?php echo $member->tel ?></span>
                    <?php endif ?>    
                    <span class="tel"><?php echo $member->tel_portable ?></span>
                </div>
            </li>        
            

                      
        


        <?php 
            
                //var_dump($member);
        endforeach;
    ?>
</ul>

<div class="add-collabs">
    <?php echo get_template_part("content","add-collabs-popup"); ?>
</div>
<div class="supp-collabs">
    <?php echo get_template_part("content","sup-collabs-popup"); ?>
</div>

<div class="bt-ajout"><?php _e("Ajouter un collaborateur"); ?></div>
