L.Control.Radius = L.Control.extend({

    options: {
        position: 'topleft',
        showMarker: true,
        showPopup: false,
        customIcon: false,
        retainZoomLevel: false,
        draggable: false
    },

    _config: {
        units: 'm', // or miles
        radiusLabel: 'Enter search distance',
    },

    initialize: function (options) {
		options = options || {};
        L.Util.extend(this.options, options);
        L.Util.extend(this._config, options);
    },

    onAdd: function (map) {
        this._container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-radius');

        // create the link - this will contain one of the icons
        var link = L.DomUtil.create('a', 'radius', this._container);
        link.href = '#';
        link.title = this._config.radiusLabel;

        // create the form that will contain the input
        var form = L.DomUtil.create('form', 'displayNone', this._container);

        // create the input, and set its placeholder text
        var searchbox = L.DomUtil.create('input', null, form);
        searchbox.type = 'text';
        searchbox.placeholder = this._config.radiusLabel;
        this.radiusbox = searchbox;

        var msgbox = L.DomUtil.create('div', 'leaflet-bar message displayNone', this._container);
        this._msgbox = msgbox;

        L.DomEvent
            .on(link, 'click', L.DomEvent.stopPropagation)
            .on(link, 'click', L.DomEvent.preventDefault)
            .on(link, 'click', function() {

                if (L.DomUtil.hasClass(form, 'displayNone')) {
                    L.DomUtil.removeClass(form, 'displayNone'); // unhide form
                    searchbox.focus();
                } else {
                    L.DomUtil.addClass(form, 'displayNone'); // hide form
                }

            })
            .on(link, 'dblclick', L.DomEvent.stopPropagation);

        L.DomEvent
            .addListener(this.radiusbox, 'keypress', this._onKeyPress, this)
            .addListener(this.radiusbox, 'keyup', this._onKeyUp, this)
            .addListener(this.radiusbox, 'input', this._onInput, this);

        L.DomEvent.disableClickPropagation(this._container);

        return this._container;
    },

    _onKeyUp: function (e) {
        var esc = 27;

        if (e.keyCode === esc) { // escape key detection is unreliable
            this.cancelSearch();
        }
    },

    _getZoomLevel: function() {
        if (! this.options.retainZoomLevel) {
            return this._config.zoomLevel;
        }
        return this._map._zoom;
    },

    _onInput: function() {
        if (this._isShowingError) {
            this.resetLink('glass');
            L.DomUtil.addClass(this._msgbox, 'displayNone');

            this._isShowingError = false;
        }
    },

    _onKeyPress: function (e) {
        var enterKey = 13;

        if (e.keyCode === enterKey) {
            e.preventDefault();
            e.stopPropagation();

            this.startSearch();
        }
    },

	startSearch: function() {
		console.log("search started");
	}

});
