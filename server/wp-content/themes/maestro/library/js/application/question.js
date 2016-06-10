App.Question = {

    buttonQuestionOpenSelector          : '.js-question-open',

    formQuestionSelector                : '.js-question-form',

    selectQuestionMatiereSelector       : '.js-question-select-matiere',
    selectQuestionCompetenceSelector    : '.js-question-select-competence',

    zoneQuestionSupportSelector         : '.js-question-support-zone',
    radioQuestionSupportSelector        : '.js-question-support-radio',
    //radioQuestionExpertiseSelector        : '.js-question-expertise-radio',     A CABLER 
    fileQuestionSelector                : '.js-question-file',
    fileQuestionResetSelector           : '.js-file-reset',
    fileQuestionNameSelector            : '.js-file-name',
    objectQuestionFieldSelector         : '.js-question-object',
    messageQuestionFieldSelector        : '.js-question-message',

    selectQuestionCompetenceName        : 'question_competence',

    tabQuestionExpertiseSelector        : '.js-question-tab-expertise',
    tabQuestionConsultationSelector     : '.js-question-tab-consultation',
    tabQuestionMaQuestionSelector       : '.js-question-tab-ma-question',
    buttonQuestionExpertiseSelector     : '.js-question-button-expertise',
    buttonQuestionConsultationSelector  : '.js-question-button-consultation',
    buttonQuestionMaQuestionSelector    : '.js-question-button-ma-question',
    submitQuestionSelector              : '.js-question-submit',
    blockQuestionErrorSelector          : '.js-question-error',

    buttonQuestionDocumentationSelector : '.js-question-documentation-button',
    buttonQuestionSupportSelector       : '.js-question-support-shortcut',
    buttonQuestionSupportNSelector      : 'js-question-support-shortcut-',

    owlCarouselSelector                 : "#owl-support",

    owlCarouselSelector2                 : "#owl-niveau-expertise",


    popupOverlaySelector                : "#layer-posez-question",

    selectedClass                       : 'selected',

    $buttonQuestionOpen                 : null,
    $formQuestion                       : null,
    $selectQuestionMatiere              : null,
    $selectQuestionCompetence           : null,
    $selectQuestionCompetenceArray      : [],
    $zoneQuestionSupport                : null,
    $radioQuestionSupport               : null,
    $fileQuestion                       : null,
    $fileQuestionReset                  : null,
    $fileQuestionName                   : null,
    $objectQuestionField                : null,
    $messageQuestionField               : null,

    $tabQuestionConsultation            : null,
    $tabQuestionMaQuestion              : null,
    $buttonQuestionConsultation         : null,
    $buttonQuestionMaQuestion           : null,
    $submitQuestion                     : null,

    $buttonQuestionDocumentation        : null,
    $buttonQuestionSupportShortcut      : null,

    $blockQuestionError                 : null,

    $owlCarousel                        : null,
    $owlCarousel2                       : null,
    $popupOverlay                       : null,


    init: function() {
        this.debug("Question : init start");

        var self = this;

        this.$buttonQuestionOpen                    = $(this.buttonQuestionOpenSelector);

        this.$formQuestion                          = $(this.formQuestionSelector);

        this.$selectQuestionMatiere                 = $(this.selectQuestionMatiereSelector);
        this.$selectQuestionCompetence              = $(this.selectQuestionCompetenceSelector);

        this.$selectQuestionCompetence.each(function(i) {
            self.$selectQuestionCompetenceArray[$(this).data('matiere-id')] = $(this);
        });

        this.$tabQuestionExpertise                 = $(this.tabQuestionExpertiseSelector);
        this.$tabQuestionConsultation               = $(this.tabQuestionConsultationSelector);
        this.$tabQuestionMaQuestion                 = $(this.tabQuestionMaQuestionSelector);
        this.$buttonQuestionExpertise            = $(this.buttonQuestionExpertiseSelector);
        this.$buttonQuestionConsultation            = $(this.buttonQuestionConsultationSelector);
        this.$buttonQuestionMaQuestion              = $(this.buttonQuestionMaQuestionSelector);

        this.$zoneQuestionSupport                   = $(this.zoneQuestionSupportSelector);
        this.$radioQuestionSupport                  = $(this.radioQuestionSupportSelector);
        this.$fileQuestion                          = $(this.fileQuestionSelector);
        this.$fileQuestionReset                     = $(this.fileQuestionResetSelector);
        this.$fileQuestionName                      = $(this.fileQuestionNameSelector);

        this.$buttonQuestionDocumentation           = $(this.buttonQuestionDocumentationSelector);
        this.$buttonQuestionSupportShortcut         = $(this.buttonQuestionSupportSelector);

        this.$blockQuestionError                    = $(this.blockQuestionErrorSelector);

        this.$objectQuestionField                   = $(this.objectQuestionFieldSelector);
        this.$messageQuestionField                  = $(this.messageQuestionFieldSelector);

        this.$owlCarousel                           = $(this.owlCarouselSelector);
        this.$owlCarousel2                           = $(this.owlCarouselSelector2);
        this.$popupOverlay                          = $(this.popupOverlaySelector);

        this.$submitQuestion                        = $(this.submitQuestionSelector);

        this.addListeners();

        var bClass = App.Utils.getBodyClass();

        if (bClass.indexOf("is_notaire") !== -1) {
            this.popupOverlayInit();
        }

        if (App.Utils.queryString['openQuestion'] == 1) {
            this.$popupOverlay.popup('show');
            this.openTabQuestionConsultation(false);
        }


        this.debug("Question : init end");

    },

    popupOverlayInit: function() {
        /* POPUP OVERLAY*/

        this.$popupOverlay.popup({
            transition: 'all 0.3s',
            scrolllock: true,
            opacity: 0.8,
            color: '#324968',
            offsettop: 10,
            vertical: top,
            onopen: this.owlCarouselInit.bind(this)

        });
    },

    owlCarouselInit: function() {

        this.$owlCarousel.owlCarousel({

            pagination: false,
            dots: false,
            navText: false,
            itemClass: 'owl-item ' + this.zoneQuestionSupportSelector.substr(1), // de étape 2 à étape 3
            onInitialized: this.addListenersAfterOwl.bind(this),
            responsive:{
                0 : {
                    items:1,
                    dots:true,
                    nav:true,

                },
                // breakpoint from 480 up
                768 : {
                    items:3,
                    dots:false,
                },
                // breakpoint from 768 up
                1200 : {
                    items:3,
                    dots:false,
                }
            }

        });

        this.$owlCarousel2.owlCarousel({

            pagination: false,
            dots: false,
            navText: false,
            itemClass: 'owl-item ' + this.zoneQuestionSupportSelector.substr(1), // TODO : de étape 1 vers étape 2
            onInitialized: this.addListenersAfterOwl.bind(this), // TODO : Initialisation différente ?
            responsive:{
                0 : {
                    items:1,
                    dots:false,
                    nav:true,
                },
                // breakpoint from 480 up
                768 : {
                    items:3,
                    dots:false,
                },
                // breakpoint from 768 up
                1200 : {
                    items:3,
                    dots:false,
                }
            }

        });

        var nonce   = document.createElement('input');
        nonce.type  = 'hidden';
        nonce.name  = 'tokenquestion';
        nonce.id    = 'tokenquestion';
        nonce.value = jsvar.question_nonce;


        // reset file list
        this.eventFileReset();
        this.$formQuestion[0].reset();
        this.$submitQuestion.attr('disabled',false);
        this.$blockQuestionError.html('');
        this.$formQuestion.append(nonce);
        if (App.Utils.device.ios) {
            $('.fileUpload').remove();
        }

    },


    /*
     * Listeners for the Question page events
     */
    addListeners: function() {
        var self = this;
        var bClass = App.Utils.getBodyClass();

        this.debug("Question : addListeners start");


        this.$selectQuestionMatiere.on("change", function(e) {
            self.eventSelectQuestionMatiereChange($(this));
        });

        this.$buttonQuestionConsultation.on('click', function(e) {
            self.openTabQuestionConsultation($(this));
        });

        this.$buttonQuestionMaQuestion.on('click', function(e) {
            // self.openTabQuestionMaQuestion($(this));
        });

        if (! App.Utils.device.ios) {

            this.$fileQuestion.on('change', function (e) {
                self.eventFileChange($(this));
            });

            this.$fileQuestionReset.on('click', function (e) {
                self.eventFileReset($(this), e);
                e.preventDefault();
                return false;
            });
        }


        if ( bClass.indexOf("is_notaire") === -1) {
            this.$buttonQuestionOpen.add(this.buttonQuestionDocumentationSelector).add(this.buttonQuestionSupportSelector).on('click', function (e) {
                App.Login.eventPanelConnexionToggle();
                App.Login.changeLoginErrorMessage("ERROR_NOT_CONNECTED_QUESTION");
                App.Login.targetUrl = (typeof App.Login.targetUrl) == "boolean" ? location.origin + location.pathname : App.Login.targetUrl;

                if (App.Utils.queryString == false) {
                    App.Login.targetUrl += "?openQuestion=1";
                } else if (!App.Utils.queryString["openQuestion"]) {
                    App.Login.targetUrl += "&openQuestion=1";
                }
            });
        } else if (bClass.indexOf("has_question_role") === -1) {
            this.$buttonQuestionOpen.on('click',function(e){
                var url = $(this).data('js-redirect');
                window.location = url;
            })
        } else {
            this.$buttonQuestionDocumentation.on('click', function(e) {
                self.$formQuestion[0].reset();
                self.$zoneQuestionSupport.removeClass(this.selectedClass);
                self.$popupOverlay.popup('show');
                self.openTabQuestionMaQuestion(false);
                self.eventButtonDocumentationClick($(this));
            });

            this.$buttonQuestionSupportShortcut.on('click', function(e) {
                self.eventButtonSupportClick($(this));
            });
        }

        this.debug("Question : addListeners end");
    },
    addListenersAfterOwl: function() {
        var self = this;
        this.debug('Question : addListenersAfterOwl');

        this.$zoneQuestionSupport                   = $(this.zoneQuestionSupportSelector);
        this.$radioQuestionSupport                  = $(this.radioQuestionSupportSelector);


        this.$zoneQuestionSupport.on('click', function(e) {
            self.eventZoneQuestionSupportClick($(this));
        });

        this.$radioQuestionSupport.on('change', function (e) {
            self.openTabQuestionMaQuestion($(this));
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        });

        this.$formQuestion.on('submit', function (e) {
            self.eventSubmitQuestion($(this));
            return false;
        });

    },

    /*
     * Event
     */

    eventFileReset: function (reset, e) {
        if (this.$fileQuestion == null) {
            this.$fileQuestion = $(this.fileQuestionSelector);
        }
        var self = this;
        if (reset != undefined) {
            var file = reset.siblings(this.fileQuestionSelector);
            file.replaceWith(file = file.clone(true));
            file.wrap('<form>').closest('form').get(0).reset();
            file.unwrap();
            this.eventFileChange(file);
        } else {
            this.$fileQuestion.each(function(i,c) {
                var file = $(c);
                file.replaceWith(file = file.clone(true));
                file.wrap('<form>').closest('form').get(0).reset();
                file.unwrap();
                self.eventFileChange(file);
            });
        }
        this.$fileQuestion = $(this.fileQuestionSelector);
    },

    eventFileChange: function (fileInput) {
        var file = fileInput[0].files[0];
        if (file && file.size > jsvar.question_max_file_size) {
            this.$blockQuestionError.text(jsvar.question_file_size_error);
            // stop action
            return false;
        } else {
            if (fileInput.val() != "") {
                fileInput.siblings(this.fileQuestionNameSelector).text(fileInput.val());
                var nextFileInput = false;
                this.$fileQuestion.each(function (i, c) {
                    var c = $(c);
                    if (c.val() == "" && c.parents('.fileUpload').first().hasClass('hidden') && !nextFileInput) {
                        c.parents('.fileUpload').first().removeClass('hidden');
                        nextFileInput = true;
                    }
                });
            } else {
                fileInput.siblings(this.fileQuestionNameSelector).text("Vide");

            }
        }
    },

    eventSelectQuestionMatiereChange: function(select) {
        var matiere = this.$selectQuestionMatiere.val();
        this.$selectQuestionCompetenceArray.forEach(function(c, i, a) {
            c.addClass('hidden');
            c.attr('name', "");
        });
        this.$selectQuestionCompetenceArray[matiere].removeClass('hidden');
        this.$selectQuestionCompetenceArray[matiere].attr('name', this.$selectQuestionCompetenceArray[matiere].data('name'));

    },

    eventZoneQuestionSupportClick: function(zone) {
        this.$zoneQuestionSupport.removeClass(this.selectedClass);
        zone.addClass(this.selectedClass);
        var radio = zone.find(this.radioQuestionSupportSelector).first();
        radio.prop("checked", true).change();
        this.openTabQuestionMaQuestion(false);
    },

    eventButtonDocumentationClick: function () {
        var min = {el: undefined, val: undefined};
        this.$radioQuestionSupport.each(function(i, el) {
            if ($(el).data('value') < min.val || min.val == undefined ) {
                min.val = $(el).data('value');
                min.el = $(el);
            }
        });
        this.eventZoneQuestionSupportClick(min.el.parents(this.zoneQuestionSupportSelector).first());
        this.$selectQuestionMatiere.val( DocumentationID).change();
        //this.eventSelectQuestionMatiereChange(false);

    },

    eventButtonSupportClick: function(button) {
        this.$popupOverlay.popup('show');
        var support = button.data('support');
        if (support == undefined) {
            for(var i = 0; i < button[0].classList.length; i++ ) {
                button[0].classList.item(i);
                var re = new RegExp(this.buttonQuestionSupportNSelector + "(\\d+)");
                var match = re.exec(button[0].classList.item(i));
                if (match && match[1]) {
                    support = match[1];
                    break;
                }
            }
        }
        var radio = this.$radioQuestionSupport.eq( support );
        this.eventZoneQuestionSupportClick(radio.parents(this.zoneQuestionSupportSelector).first());

    },

     openTabQuestionExpertise: function(button) {
        this.$buttonQuestionExpertise.addClass('open');
        this.$tabQuestionExpertise.addClass('open');
        this.$buttonQuestionConsultation.removeClass('open');
        this.$tabQuestionConsultation.removeClass('open');
        this.$buttonQuestionMaQuestion.removeClass('open');
        this.$tabQuestionMaQuestion.removeClass('open');
    },


    openTabQuestionConsultation: function(button) {
        this.$buttonQuestionConsultation.addClass('open');
        this.$tabQuestionConsultation.addClass('open');
        this.$buttonQuestionMaQuestion.removeClass('open');
        this.$tabQuestionMaQuestion.removeClass('open');
        this.$buttonQuestionExpertise.removeClass('open');
        this.$tabQuestionExpertise.removeClass('open');
    },

    openTabQuestionMaQuestion: function(button) {
        this.$buttonQuestionConsultation.removeClass('open');
        this.$tabQuestionConsultation.removeClass('open');
        this.$buttonQuestionMaQuestion.addClass('open');
        this.$tabQuestionMaQuestion.addClass('open');
        this.$buttonQuestionExpertise.removeClass('open');
        this.$tabQuestionExpertise.removeClass('open');
    },

    eventSubmitQuestion: function(form) {
        var supportFieldId = jsvar.question_support,
            matiereFieldId = jsvar.question_matiere,
            competenceFieldId = jsvar.question_competence,
            objectFieldId = jsvar.question_objet,
            messageFieldId = jsvar.question_message;

        var formdata = new FormData();
        var nbFiles = 0;
        nbFiles = this.$fileQuestion.length;
        if (nbFiles > 0) {
            this.$fileQuestion.each(function () {
                var file = $(this).get(0).files[0];
                if (file) {
                    if (formdata && (parseInt(file.size) <= parseInt(jsvar.question_max_file_size))) {
                        formdata.append(jsvar.question_fichier + '[]', file);
                    }
                }
            });
        }

        var d = {
            support : $(this.radioQuestionSupportSelector + ':checked').first().val(),
            matiere : this.$selectQuestionMatiere.first().val(),
            competence : $('*[name="' + competenceFieldId + '"]').first().val(),
            object : this.$objectQuestionField.first().val(),
            message : this.$messageQuestionField.first().val()
        };

        if(!d.support) {
            this.$blockQuestionError.text('Merci de sélectionner un support/délai');
            this.openTabQuestionConsultation();
            return false;
        }
        if(!d.matiere) {
            this.$blockQuestionError.text('Merci de sélectionner une matière');
            this.openTabQuestionMaQuestion();
            return false;
        }
        if(!d.object) {
            this.$blockQuestionError.text('Merci de bien remplir le champ "Objet de la question"');
            this.openTabQuestionMaQuestion();
            this.$objectQuestionField.focus();

            // stop action
            return false;
        }

        formdata.append("action", 'add_question');
        formdata.append(supportFieldId, d.support );
        formdata.append(matiereFieldId, d.matiere );
        formdata.append(competenceFieldId, d.competence );
        formdata.append(objectFieldId, d.object );
        formdata.append(messageFieldId, d.message );
        this.$messageQuestionField.html('');

        if (parseInt(nbFiles) > parseInt(jsvar.question_nb_file)) {
            this.$messageQuestionField.html(jsvar.question_nb_file_error);

            // stop action
            return false;
        }
        this.$submitQuestion.attr('disabled',true);

        jQuery.ajax({
            type: 'POST',
            url: jsvar.ajaxurl,
            data: formdata,
            processData: false,
            contentType: false,
            success: this.successQuestion.bind(this)
        });

    },

    successQuestion: function (data) {

        data = JSON.parse(data);
        // show message response
        var content = $(document.createElement('ul'));
        if ( data != undefined && data.error != undefined && Array.isArray(data.error) ) {
            data.error.forEach(function(c, i, a) {
                content.append($(document.createElement('li')));
                content.find('li').last().text(c);
            });
            this.$submitQuestion.attr('disabled',false);
        }else {
            content.append($(document.createElement('li')));
            content.find('li').last().addClass('success').text(data);
            window.setTimeout( (function() {
                this.$submitQuestion.attr('disabled',false);
                this.$popupOverlay.popup('hide');
                this.$formQuestion[0].reset();
                this.$zoneQuestionSupport.removeClass(this.selectedClass);
                this.openTabQuestionConsultation();
                this.$blockQuestionError.html('');
            }).bind(this), 1500);

        }
        this.$blockQuestionError.html('');
        this.$blockQuestionError.append(content);


        return false;
    },

    debug: function(t) {
        App.debug(t);
    }
};

'use strict';
