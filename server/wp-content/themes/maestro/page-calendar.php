<?php
$calendar = $data['calendar'];
$month = $data['month'];
$year = $data['year'];
?>
<?php get_header(); ?>
    <div id="content" class="page page-calendar">

        <div class="breadcrumbs">
            <div class="wrap cf">
                <?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
            </div>
        </div>

        <div id="inner-content" class="wrap-desktop cf">

            <div id="main" class="cf" role="main">
                 <h1 class="h1">Calendrier des formations</h1>

                <div id="calendar">
                    <div class="calendar__wrapper--header">
                        <div class="calendar__header--navigation">
                            <a href="/calendrier-des-formations/<?php echo $data['prev_month']['month'] ?>-<?php echo $data['prev_month']['year'] ?>" class="calendar__button calendar__button--previous">
                                &nbsp;
                            </a>
                            <div class="calendar__block--currentmonth">
                                <?php
                                setlocale(LC_TIME, 'fr_FR');
                                $actual_month = utf8_encode(strftime('%B', DateTime::createFromFormat('!m',$month)->getTimestamp()));
                                $actual_month = ($year == strftime('%Y') ) ? $actual_month : $actual_month . ' ' . $year;
                                echo $actual_month;
                                ?>
                            </div>
                            <a href="/calendrier-des-formations/<?php echo $data['next_month']['month'] ?>-<?php echo $data['next_month']['year'] ?>" class="calendar__button calendar__button--next">
                                &nbsp;
                            </a>
                        </div>
                        <ul class="calendar__header--weekdays"><!--
                            <?php for ($i=0;$i<7;$i++) : ?>
                             --><li class="calendar__weekdays" data-col-weekday="<?php echo $i ; ?>">
                                    <?php echo strftime('%A', strtotime('next Monday +' . $i . 'days')); ?>
                                </li><!--
                            <?php endfor; ?>

                     --></ul>
                    </div>
                    <div class="calendar__wrapper--body">
                        <ul class="calendar__body"><!--
                            <?php foreach ($calendar as $date => $day): ?>
                             --><li class="calendar__day <?php echo $day['today'] ? 'calendar__day--today' : '' ?> <?php echo !$day['in_month'] ? 'calendar__day--greyed' : '' ?>" data-date="<?php echo $day; ?>">
                                    <div class="calendar__day-side">
                                        <div class="calendar__day-number">
                                            <?php echo $day['date']->format('j') ; ?>
                                        </div>
                                        <div class="calendar__day-name">
                                            <?php echo strftime('%a', $day['date']->getTimestamp()); ?>
                                        </div>
                                        <?php if (!empty($day['event'])) : ?>
                                            <div class="calendar__day-event calendar__day-event--tablet js-calendar-ellipsis" title="<?php echo $day['event'] ; ?>"><?php echo $day['event']//truncate($day['event'], 43, ' ...') ; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="calendar__day-content">
                                        <?php if (!empty($day['event'])) : ?>
                                            <div class="calendar__day-event calendar__day-event--mobile" title="<?php echo $day['event'] ; ?>"><?php echo $day['event']//truncate($day['event'], 43, ' ...') ; ?></div>
                                        <?php endif; ?>
                                        <?php
                                        $is_scrollable = false;
                                        if (count($day['sessions']) > 1) {
                                            $is_scrollable = true;
                                        }
                                        /* Si on ne tronque pas en CSS/JS on remet cette condition
                                         * else if (count($day['sessions']) == 1 ) {
                                            $first = array_values($day['sessions'])[0];
                                            $text = $first['name'];
                                            $nblines = strlen($text) / 26;
                                            $nblines += ($first['organisme']) ? (strlen($first['organisme']->name) / 26) : 0;
                                            if ($nblines > 7) {
                                                $is_scrollable = true;
                                            }
                                        }*/
                                        ?>
                                        <?php if ($is_scrollable) : ?>
                                            <div class="calendar__day-sessions-scrollbar">
                                                <div class="calendar__day-sessions-scrollbar--bar"></div>
                                            </div>
                                        <?php endif; ?>
                                        <ul class="calendar__day-sessions <?php echo $is_scrollable ? 'calendar__day-sessions--scrollable' : '' ; ?>">
                                            <?php if (is_array($day['sessions'])): ?>
                                            <?php foreach ($day['sessions'] as $index => $session) : ?>
                                                <li 
                                                    class="calendar__session js-calendar__session" 
                                                    data-session="<?php echo $session['id'] ; ?>"
                                                    data-block="calendar__session-block-<?php echo $date ; ?>"
                                                    style="background-color: <?php echo !empty($session['matiere']->color) ? $session['matiere']->color : '#000' ?>;"
                                                >
                                                    <div class="calendar__session-name calendar__session-name--name js-calendar-ellipsis" title="<?php echo $session['name'] ; ?>"><?php echo $session['name'] ; ?></div><!--
                                                    <?php if ($session['organisme']) : ?>
                                                        --><div class="calendar__session-name calendar__session-name--organisme"><?php echo $session['organisme']->is_cridon ? strtoupper($session['organisme']->name) : $session['organisme']->name; ?></div>
                                                    <?php endif; ?><!--
                                                    --><div class="calendar__session-content">
                                                        <div class="calendar__session-content--header">
                                                            <img class="calendar__session-matiere--icon" src="<?php echo $session['matiere']->picto; ?>" alt="<?php echo $session['matiere']->label ; ?>" width="30" height="30">
                                                            <span class="calendar__session-content--name"><?php echo $session['name'] ; ?></span>
                                                        </div>
                                                        <div class="calendar__session-content--body">
                                                            <?php if ($session['organisme']) : ?>
                                                                <div class="calendar__session-content--place">
                                                                    <?php echo $session['organisme']->is_cridon ? strtoupper($session['organisme']->name) : $session['organisme']->name; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($session['time']) : ?>
                                                                <div class="calendar__session-content--time"><?php echo $session['time'] ; ?></div>
                                                            <?php endif; ?>
                                                            <?php if ($session['url']) : ?>
                                                                <a href="<?php echo $session['url'] ; ?>" class="calendar__session-content--more">En savoir plus</a>
                                                            <?php endif; ?>
                                                            <?php if ($session['action'] && $session['action_label']) : ?>
                                                            <a href="<?php echo $session['action'] ; ?>" class="calendar__session-content-button"><?php echo $session['action_label'] ; ?></a>
                                                            <?php endif; ?>
                                                            <?php if ($session['contact_organisme']) : ?>
                                                            <hr/>
                                                            <div class="calendar__session-content-chambre">
                                                                <div class="calendar__session-content-chambre--name"><?php echo $session['organisme']->name ; ?></div>
                                                                <?php if ($session['organisme']->phone_number) : ?>
                                                                <div class="calendar__session-content-chambre--telephone"><span>Tel. :</span>
                                                                    <a href="tel:<?php echo $session['organisme']->phone_number ; ?>" ><?php echo $session['organisme']->phone_number ; ?></a>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if ($session['organisme']->email) : ?>
                                                                <div class="calendar__session-content-chambre--email"><span>Email :</span>
                                                                    <a href="mailto:<?php echo $session['organisme']->email ; ?>" ><?php echo $session['organisme']->email ; ?></a>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="calendar__session-block js-calendar__session-block" id="calendar__session-block-<?php echo $date ; ?>">
                                        <div class="calendar__session-block-button--close js-calendar__session-block-button--close">X</div>
                                        <div class="calendar__session-block-content">

                                        </div>
                                    </div>
                                </li><!--
                            <?php endforeach; ?>
                         --></ul>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?php get_footer(); ?>
