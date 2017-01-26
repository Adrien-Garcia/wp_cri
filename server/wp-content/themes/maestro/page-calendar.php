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
                            <a href="calendrier-des-formations?month=<?php echo $data['prev_month']['month'] ?>&year=<?php echo $data['prev_month']['year'] ?>" class="calendar__button calendar__button--previous">
                                &nbsp;
                            </a>
                            <div class="calendar__block--currentmonth">
                                <?php
                                setlocale(LC_TIME, 'fr_FR');
                                $actual_month = strftime('%B', DateTime::createFromFormat('!m',$month)->getTimestamp());
                                $actual_month = ($year == strftime('%Y') ) ? $actual_month : $actual_month . ' ' . $year;
                                echo $actual_month;
                                ?>
                            </div>
                            <a href="/calendrier-des-formations?month=<?php echo $data['next_month']['month'] ?>&year=<?php echo $data['next_month']['year'] ?>" class="calendar__button calendar__button--next">
                                &nbsp;
                            </a>
                        </div>
                        <ul class="calendar__header--weekdays">
                            <?php for ($i=0;$i<7;$i++) : ?>
                                <li class="calendar__weekdays" data-col-weekday="<?php echo $i ; ?>">
                                    <?php echo strftime('%A', strtotime('next Monday +' . $i . 'days')); ?>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                    <div class="calendar__wrapper--body">
                        <ul class="calendar__body">
                            <?php foreach ($calendar as $date => $day): ?>
                                <li class="calendar__day <?php echo $day['today'] ? 'calendar__day--today' : '' ?> <?php echo !$day['in_month'] ? 'calendar__day--greyed' : '' ?>" data-date="<?php echo $day; ?>">
                                    <div class="calendar__day-side">
                                        <div class="calendar__day-number">
                                            <?php echo $day['date']->format('j') ; ?>
                                        </div>
                                        <div class="calendar__day-name"><?php echo $day['date']->format('D') ; ?></div>
                                        <?php if (!empty($day['event'])) : ?>
                                            <div class="calendar__day-event calendar__day-event--tablet" title="<?php echo $day['event'] ; ?>"><?php echo $day['event']//truncate($day['event'], 43, ' ...') ; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="calendar__day-content">
                                        <?php if (!empty($day['event'])) : ?>
                                            <div class="calendar__day-event calendar__day-event--mobile" title="<?php echo $day['event'] ; ?>"><?php echo $day['event']//truncate($day['event'], 43, ' ...') ; ?></div>
                                        <?php endif; ?>
                                        <ul class="calendar__day-sessions">
                                            <?php foreach ($day['sessions'] as $index => $session) : ?>
                                                <li class="calendar__session" style="background-color: <?php echo !empty($session['matiere']->color) ? $session['matiere']->color : '#000' ?>;">
                                                    <div class="calendar__session-name"><?php echo $session['short_name'] ; ?></div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?php get_footer(); ?>
