	// An Application object constructor function
	function PSApplication(options) {
		
		// extend this instance with all the options provided
		_.extend(this, options);
		
		// a place to store initializer functions
		this._initializers = jQuery.Deferred();
	};
	
	// Application instance methods go here
	_.extend( PSApplication.prototype, Backbone.Events, {
		addInitializer: function(initializer){
			this._initializers.done(initializer);	
		},
		
		start: function(args){
			// get the complete args list as an array
			args = Array.prototype.slice.call(arguments);
			
			// resolve the initializers promise
			this._initializers.resolveWith(this, args);
			
			// trigger the start event
			this.trigger("start", args);
		}
	});
	
	var wc_ps_app = new PSApplication();