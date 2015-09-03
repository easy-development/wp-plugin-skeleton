jQuery(document).ready(function(){
  EasyDevelopmentSkeleton.Init();

  jQuery('[easy-development-skeleton-fast-copy]').not(".easy-development-dynamic-init").each(function(){
    jQuery(this).zclip({
      path : AppHelper.zeroClipboardSWF,
      copy : function(){
        var copyElement = jQuery(jQuery(this).attr("easy-development-skeleton-fast-copy")),
            id          = copyElement.attr( 'id' ),
            sel         = '#wp-' + id + '-wrap',
            container   = jQuery( sel );

        if(container.length > 0) {
          var editor    = tinyMCE.get( id );
          if ( editor && container.hasClass( 'tmce-active' ) ) {
            editor.save();
          }
        }

        return copyElement.val();
      },
      afterCopy:function(){
        jQuery(this).html('Copied to clipboard');

        var objectInstance = this;

        setTimeout(function(){
          jQuery(objectInstance).html("Copy");
        },4000);
      }
    });
    jQuery(this).addClass("easy-development-dynamic-init");
  });
});

var EasyDevelopmentSkeleton = {

  Init : function() {
    var objectInstance = this;

    this.EventManager.registerEvent('displayMovement');

    this.Component.Init(objectInstance);

    jQuery(window).bind('resize scroll', function(){
      objectInstance.EventManager.triggerEvent('displayMovement');
    });

    jQuery(window).bind('resize', function(){
      objectInstance.EventManager.triggerEvent('displayResize');
    });
  }

};

EasyDevelopmentSkeleton.EventManager = {

  eventList : {},

  init : function() {

  },

  registerEvent : function(event_identifier) {
    if(typeof this.eventList[event_identifier] == "undefined")
      this.eventList[event_identifier] = [];
  },

  unRegisterEvent : function(event_identifier) {
    if(typeof this.eventList[event_identifier] != "undefined")
      delete this.eventList[event_identifier];
  },

  triggerEvent  : function(event_identifier, data) {
    data = typeof data != "undefined" ? data : {};

    if(typeof this.eventList[event_identifier] != "undefined") {
      var currentEventInformation = this.eventList[event_identifier];

      for(var currentListenerIndex in currentEventInformation) {
        var currentListener       = currentEventInformation[currentListenerIndex],
            currentListenerMethod = currentListener['method'];

        currentListener.object[currentListenerMethod].call(currentListener.object, data);
      }
    }

  },

  listenEvent : function(event_identifier, object, method) {
    if(typeof this.eventList[event_identifier] == "undefined")
      this.registerEvent(event_identifier);

    this.eventList[event_identifier][this.eventList[event_identifier].length] = {
      'object' : object,
      'method' : method
    };
  }
};

EasyDevelopmentSkeleton.Component = {

  activeComponents : {

  },

  skeletonInstance : {},
  processedClass   : 'easy-development-component-init',

  Init : function(skeletonInstance) {
    this.skeletonInstance = skeletonInstance;

    var objectInstance = this;

    jQuery.each(this.skeletonInstance.Components, function(name, component){

      jQuery(component.containerIdentifier).each(function(){
        if(!jQuery(this).hasClass(objectInstance.processedClass)) {
          jQuery(this).addClass(objectInstance.processedClass);
          objectInstance.Factory(jQuery(this), name);
        }
      });

    });
  },

  Factory : function(componentContainerObject, componentName) {
    if(typeof this.activeComponents[componentName] == "undefined")
      this.activeComponents[componentName] = [];

    var objectInstance    = this,
        componentInstance = {}; // Hard Factory Reset

    componentInstance = jQuery.extend(1, {}, this.skeletonInstance.Components[componentName]);

    this._factoryInitComponent(componentContainerObject, componentName, componentInstance);
  },

  _factoryInitComponent : function(componentContainerObject, componentName, componentInstance) {
    componentInstance.Init(componentContainerObject, this.skeletonInstance);

    this.activeComponents[componentName][this.activeComponents[componentName].length] = componentInstance;
  },

  ClearInitForClone : function(cloneObject) {
    cloneObject.find('.' + this.processedClass).removeClass(this.processedClass);
  }

};

EasyDevelopmentSkeleton.Components = {

  WordpressImageTarget : {

    skeletonInstance : {},
    alias                     : "wordpress_image_target",
    containerObject           : {},
    containerIdentifier       : "[data-ed-wordpress-image-target]",
    containerTargetAttr       : 'data-ed-wordpress-image-target',
    imageInputTargetObject    : {},
    sendToEditorTemporaryCopy : false,

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject  = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.imageInputTargetObject = this.containerObject.parents("form:first").find(
          this.containerObject.attr(this.containerTargetAttr)
      );

      this.initPrimaryFunctionality();

      if(this.containerObject.next().is(this.imageInputTargetObject))
        this.initArrangeFunctionality();
    },

    initPrimaryFunctionality : function() {
      var objectInstance = this;

      this.containerObject.bind("click", function(event){
        objectInstance.sendToEditorTemporaryCopy = window.send_to_editor;
        window.send_to_editor = function(html) {
          objectInstance.imageInputTargetObject.val(
              jQuery('img', html).attr('src')
          ).trigger("change");

          tb_remove();

          window.send_to_editor = objectInstance.sendToEditorTemporaryCopy;
        };

        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );

        return false;
      });
    },

    initArrangeFunctionality : function() {
      this.skeletonInstance.EventManager.listenEvent('displayResize', this, 'arrangeInputAccordingToButton');
      this.arrangeInputAccordingToButton();
    },

    arrangeInputAccordingToButton : function() {
      var commonContainer = this.containerObject.parent();

      this.imageInputTargetObject
          .css("display", "inline-block")
          .innerWidth(
              parseInt(commonContainer.width(), 10) -
              parseInt(this.containerObject.outerWidth(true), 10) -
              (
                  this.containerObject.outerWidth() - this.containerObject.innerWidth()
              ) -
              (
                  this.imageInputTargetObject.outerWidth() - this.imageInputTargetObject.innerWidth()
              )
          );

    }

  },

  Slider : {

    skeletonInstance : {},
    alias                  : "component_slider",
    containerObject        : {},
    containerIdentifier    : ".component-slider",

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this._init();
    },

    _init : function() {
      var objectInstance = this;

      this.handleDisplay();

      // If we're using a LESS Development version, we will handle the display again shortly.
      if(jQuery('link[rel="stylesheet/less"]').length > 0)
        setTimeout(function(){ objectInstance.handleDisplay(); }, 100);

      this.skeletonInstance.EventManager.listenEvent('displayResize', this, 'handleDisplay');
    },

    handleDisplay : function() {
      var height = jQuery(window).height();

      if(this.containerObject.next().hasClass("component-navigation"))
        height -= this.containerObject.next().height();

      this.containerObject.find(".carousel-inner > .item").css("height", height);
    }

  },

  ElegantHeight : {

    skeletonInstance      : {},
    alias                       : "component_elegant_height",
    containerObject             : {},
    containerIdentifier         : "[data-elegant-height]",
    targetAttributeIdentifier   : "data-elegant-height",
    targetObjectList            : {},

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.targetObjectList = this.containerObject.find(this.containerObject.attr(this.targetAttributeIdentifier));

      this.ArrangeTargetObjectList();

      var objectInstance = this;
      this.targetObjectList.find("img").load(function(){
        objectInstance.ArrangeTargetObjectList();
      });

      this.skeletonInstance.EventManager.listenEvent('displayResize', this, 'ArrangeTargetObjectList');
    },

    ArrangeTargetObjectList : function() {
      this.targetObjectList.css("height", "auto");

      var minHeight = Math.ceil(parseFloat(this.targetObjectList.eq(0).css("height")));

      this.targetObjectList.each(function(){
        minHeight = parseFloat(jQuery(this).css("height")) > minHeight ?
            Math.ceil(parseFloat(jQuery(this).css("height")))
            : minHeight;
      });

      this.targetObjectList.css("height", minHeight);
    }

  },

  EasyMath : {

    skeletonInstance                    : {},
    alias                                     : "component_easy_math",
    containerObject                           : {},
    containerIdentifier                       : "[data-component-easy-math]",
    containerDisplayTargetAttributeIdentifier : "data-component-easy-math",
    displayTargetIdentifier                   : "",
    displayTargetObject                       : {},
    operationTargetOperationAttribute         : "data-component-easy-math-action",
    operationTargetIdentifier                 : "[data-component-easy-math-action]",
    operationTargetObjectList                 : {},
    containerMinimumValueAttribute            : "data-component-easy-math-min-value",
    minimumValue                              : 0,

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.displayTargetIdentifier   = this.containerObject.attr(this.containerDisplayTargetAttributeIdentifier);
      this.displayTargetObject       = this.containerObject.find(this.displayTargetIdentifier);

      this.operationTargetObjectList = this.containerObject.find(this.operationTargetIdentifier);

      this.minimumValue              = (
          typeof this.containerObject.attr(this.containerMinimumValueAttribute) !== "undefined" ?
              parseFloat(this.containerObject.attr(this.containerMinimumValueAttribute)) :
              this.minimumValue
          );

      var objectInstance = this;

      this.operationTargetObjectList.bind("click touchstart", function(event){
        event.stopImmediatePropagation();
        event.preventDefault();

        objectInstance.UpdateWithCalculation(jQuery(this).attr(objectInstance.operationTargetOperationAttribute));
      });
    },

    UpdateWithCalculation : function(calculation) {
      var currentValue = this.displayTargetObject.is(":input") ? this.displayTargetObject.val() : this.displayTargetObject.html();
      currentValue = parseFloat(currentValue);

      var result = eval(currentValue + calculation);

      if(result < this.minimumValue)
        result = this.minimumValue;

      if(this.displayTargetObject.is(":input"))
        this.displayTargetObject.val(result);
      else
        this.displayTargetObject.html(result);
    }

  },

  LabelActiveClass : {

    skeletonInstance                    : {},
    alias                                     : "component_label_active_class",
    containerObject                           : {},
    containerIdentifier                       : "[data-label-active-class]",
    containerActiveClassAttribute             : "data-label-active-class",
    activeClass                               : '',
    correspondingInputObject                  : {},

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.activeClass = this.containerObject.attr(this.containerActiveClassAttribute);

      if(typeof this.containerObject.attr("for") !== "undefined") {
        var inputObject = jQuery("#" + this.containerObject.attr("for"));

        if(inputObject.length > 0) {
          this.correspondingInputObject = inputObject;
          this._checkCorrespondingInputObject();

          var objectInstance = this;

          this.correspondingInputObject.bind(this.alias, function(){
            objectInstance._checkCorrespondingInputObject();
          });

          this.correspondingInputObject.bind("change", function(){
            jQuery('input[name="' + objectInstance.correspondingInputObject.attr("name") + '"]').trigger(objectInstance.alias);
          });
        }
      }
    },

    _checkCorrespondingInputObject : function() {
      if(this.correspondingInputObject.is(":checked"))
        this.containerObject.addClass(this.activeClass);
      else
        this.containerObject.removeClass(this.activeClass);
    }

  },

  FieldSetContainer : {

    skeletonInstance       : {},
    alias                  : "component_field_set_container",
    containerObject        : {},
    containerIdentifier    : ".easy-development-field-set-container",
    fieldSetObjectList     : {},
    fieldSetTypeAttribute  : 'data-multiple-field-type',
    fieldSetIdentifier     : '[data-multiple-field-type]',
    fieldSetAddIdentifier  : '[data-multiple-field-type="add"]',
    fieldCountAttribute    : 'data-field-key-count',
    fieldCount             : 0,
    addNewTemplateLabel      : 'Add New',
    addNewTemplateAttribute  : 'data-multiple-field-label',
    sortableFields           : 0,
    sortableFieldsAttribute  : 'data-multiple-field-sortable',

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.fieldSetObjectList = this.containerObject.find("> " + this.fieldSetIdentifier).not(this.fieldSetAddIdentifier);
      this.fieldCount         = parseInt(this.containerObject.attr("data-field-key-count"));

      if(typeof this.containerObject.attr(this.addNewTemplateAttribute) != "undefined")
        this.addNewTemplateLabel = this.containerObject.attr(this.addNewTemplateAttribute);

      if(typeof this.containerObject.attr(this.sortableFieldsAttribute) != "undefined")
        this.sortableFields = parseInt(this.containerObject.attr(this.sortableFieldsAttribute));

      this._addContainerToggleDisplayFeature();

      var objectInstance = this;
      objectInstance._addDeleteFunctionality(this.containerObject.find("> " + this.fieldSetAddIdentifier));

      this.containerObject.find("> fieldset").each(function(){
        objectInstance._addDeleteFunctionality(jQuery(this));

        if(objectInstance.sortableFields == true)
          jQuery(this).prepend('<span class="sortable-drag-handle">Order</span>');
      });

      if(this.sortableFields == true) {
        this.containerObject.sortable({
          handle: '.sortable-drag-handle'
        });

        var formObject = this.containerObject.parents("form:first");

        formObject.bind("submit", function(event){
          if(!jQuery(this).hasClass("force-submit")) {
            event.preventDefault();

            formObject.find(objectInstance.containerIdentifier).each(function(){
              var orderCounter = 1;
              jQuery(this).find('> fieldset').each(function(){
                jQuery(this).find(".hidden-order-helper").val(orderCounter);
                orderCounter++;
              });
            });

            jQuery(this).addClass("force-submit");
            jQuery(this).submit();
          }
        });
      }
    },

    _addContainerToggleDisplayFeature : function() {
      var objectInstance      = this,
          addDisplayContainer = '<div class="field-set-add-container">' +
                                  '<span class="btn btn-primary field-set-add">' + this.addNewTemplateLabel + '</span>' +
                                '</div>';

      var defaultTemplateObject = this.containerObject.find("> " + this.fieldSetAddIdentifier);
          defaultTemplateObject.hide();
          defaultTemplateObject.before(addDisplayContainer);
      var addNewTemplateItemObject = defaultTemplateObject.parent().find('> .field-set-add-container > .field-set-add');
          addNewTemplateItemObject.unbind("click.addTemplateItem").bind("click.addTemplateItem", function(){
        objectInstance._actionAddNewTemplateItem(jQuery(this).parent());
      });

      setTimeout(function(){
        addNewTemplateItemObject.trigger("click.addTemplateItem");
      }, 500);
    },

    _actionAddNewTemplateItem : function(addNewTemplateItemObject) {
      addNewTemplateItemObject
          .parent().find('> ' + this.fieldSetAddIdentifier)
          .clone(true).insertBefore(addNewTemplateItemObject);

      var objectInstance = this;
      var newTemplateItemObject = addNewTemplateItemObject.prev();
          newTemplateItemObject.removeAttr(this.fieldSetTypeAttribute);
          newTemplateItemObject.hide();

      newTemplateItemObject.find("fieldset").not(this.fieldSetAddIdentifier).remove();
      newTemplateItemObject.find(":input[name]").each(function(){
        var nameAttribute    = jQuery(this).attr("name"),
            currentCloneTime = parseInt(new Date().getTime() / 1000);

        nameAttribute  = nameAttribute.replace('[new]', '[new-' + currentCloneTime + ']');
        jQuery(this).attr("name", nameAttribute);
      });

      newTemplateItemObject.slideDown("slow", function(){
        objectInstance._addDeleteFunctionality(jQuery(this));
      });
    },

    _addDeleteFunctionality : function(templateItemObject) {
      if(!(templateItemObject.find(".delete-action-container").length > 0))
        templateItemObject.prepend('<span class="delete-action-container">X</span>');

      var objectInstance = this,
          deleteActionContainer = templateItemObject.find(".delete-action-container");
      deleteActionContainer
          .unbind("click.survey-engage-delete")
          .bind("click.survey-engage-delete", function(){
        var fieldSetObject = jQuery(this).parent('fieldset');

        fieldSetObject.slideUp("slow", function(){
          jQuery(this).find(":input").val("");
        });

      });
    }

  },

  DatetimeInput : {

    skeletonInstance                          : {},
    alias                                     : "component_datetime_input",
    containerObject                           : {},
    containerIdentifier                       : ".easy-development-skeleton-datetime",

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.containerObject.datetimepicker({
        locale          : 'en-gb',
        format          : 'YYYY-MM-DD HH:MM:ss',
        sideBySide      : true
      });
      this.containerObject.focus().blur();
    }

  },

  ImageCropperUtility : {

    skeletonInstance                          : {},
    alias                                     : "component_image_cropper_utility",
    containerObject                           : {},
    containerIdentifier                       : "[data-image-cropper-utility]",
    containerCropperSetupInformationAttribute : "data-image-cropper-utility",
    containerCropperImageTargetInputAttribute : 'data-image-cropper-response',
    cropperSetupInformation                   : {},
    formObject                                : {},
    imageTargetInputObject                    : {},
    dynamicOptionsMAP                         : {},

    primaryImageObject : {},

    imagePathInputObject       : {},
    dynamicValueTargetObject   : {},

    isCropperActive : false,

    Init : function(componentContainerObject, skeletonInstance) {
      this.containerObject        = componentContainerObject;
      this.skeletonInstance = skeletonInstance;
      this.containerObject.data(this.alias, this);

      this.cropperSetupInformation = jQuery.parseJSON(this.containerObject.attr(this.containerCropperSetupInformationAttribute));
      this.formObject = this.containerObject.parents("form:first");
      this.imageTargetInputObject   = this.formObject.find('[' + this.containerCropperImageTargetInputAttribute + ']');
      this.imagePathInputObject     = this.formObject.find('[name="' + this.cropperSetupInformation.target + '"]');
      this.dynamicValueTargetObject = this.formObject.find('[name="' + this.cropperSetupInformation.dynamicValueTarget + '"]');
      this.dynamicOptionsMAP        = this.cropperSetupInformation.dynamicOptions;

      this._initContainer();
      this._initImageHandler();
      this._initCropperHandler();
    },

    _initContainer : function() {
      this.containerObject.append('<img class="cropper" style="display:block;"/>');
    },

    _initImageHandler : function() {
      var objectInstance = this;

      this.primaryImageObject = this.containerObject.find("> img:first");
      this.primaryImageObject.attr('src', this.imagePathInputObject.val());
      this.primaryImageObject.bind("load." + this.alias, function(){
        objectInstance._handleImageChangeAdjustments();
      });
    },

    _handleImageChangeAdjustments : function() {

      this.primaryImageObject.css("width", this.primaryImageObject[0].naturalWidth)
                             .css("height", this.primaryImageObject[0].naturalHeight);
      this.containerObject.css("width", this.primaryImageObject[0].naturalWidth)
                          .css("height", this.primaryImageObject[0].naturalHeight)
                          .css("display", "block")
                          .css("margin", "0 auto");
    },

    _initCropperHandler : function() {
      var objectInstance = this;

      this.dynamicValueTargetObject.bind('change.' + this.alias, function() {
        if(objectInstance.isCropperActive) {
          objectInstance.primaryImageObject.cropper(
              "setData",
              objectInstance.dynamicOptionsMAP[objectInstance.dynamicValueTargetObject.val()]
          );
        }
      });

      this.imagePathInputObject.bind('change.' + this.alias, function() {
        // This will trigger an "chain of events, next step is _handleImageChangeAdjustments
        if(objectInstance.isCropperActive) {
          objectInstance.primaryImageObject.cropper("replace", jQuery(this).val());
        } else {
          objectInstance.primaryImageObject.attr('src', jQuery(this).val());
          objectInstance._initCropperFunctionality();
        }
      });

      this._initCropperFunctionality();
    },

    _initCropperFunctionality : function() {
      if(this.primaryImageObject.attr('src') == '')
        return;

      var objectInstance = this,
          data = objectInstance.dynamicOptionsMAP[objectInstance.dynamicValueTargetObject.val()];

      this.primaryImageObject.cropper({
        resizable  : false,
        dragCrop   : false,
        data: data,
        done: function(data) {
          objectInstance.imageTargetInputObject.val(JSON.stringify(data));
        },
        built : function() {
          jQuery(window).trigger("resize");

          setTimeout(function(){
            jQuery(window).trigger("resize");
          }, 10);
        }
      });

      this.isCropperActive = true;
    }

  }

};

