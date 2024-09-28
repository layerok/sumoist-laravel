(function(){
    // Мой первый плагин на js
    this.layerok = function(){

        var defaults = {
            trigger: '[data-layerok]'
        }

        this.trigger = null;
        this.target  = null;
        this.closeButton = null;

        // Create options by extending defaults with the passed in arugments
        if (arguments[0] && typeof arguments[0] === "object") {
            this.options = extendDefaults(defaults, arguments[0]);
        }else{
            this.options = defaults;
        }

        this.initEventListeners();



    }

    // Utility method to extend defaults with user options
    function extendDefaults(source, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                source[property] = properties[property];
            }
        }
        return source;
    }


    layerok.prototype.open = function(e){
        var _ = this;
        _.target.classList.add('flex');
        _.target.classList.add('z-max');
        _.target.classList.remove('dn');
    }

    layerok.prototype.close = function(){
        var _ = this;
        _.target.classList.remove('flex');
        _.target.classList.remove('z-max');
        _.target.classList.add('dn');
    }

    layerok.prototype.initEventListeners = function() {

        var _ = this;

        _.trigger = document.querySelectorAll(_.options.trigger);
        if(!_.trigger.length > 0) return;
        _.target  = document.querySelector(_.trigger[0].dataset.layerok);
        if(_.target === undefined) return;
        _.closeButton = _.target.querySelectorAll('[data-layerok-dismiss]');



        _.target.addEventListener('click', function(e){
            if(e.target.dataset.hasOwnProperty('layerokModal')){
                _.close();
            }
        });

        _.closeButton.forEach(function(elem){
            elem.addEventListener('click', function(){
                _.close();
            });
        })

        _.trigger.forEach(function(elem){
            elem.addEventListener('click', function(e){
                e.preventDefault();
                _.open();
            });
        })

    }

}())


