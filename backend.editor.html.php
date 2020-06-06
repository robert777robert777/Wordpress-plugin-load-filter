<script>var filters</script>
<script>filters = <?php /*include(plugin_dir_path(__FILE__) .  "1.json");*/echo get_option('selective_plugin_loading'); ?></script>
<script>if (!filters) filters = [];</script>
<script>var plugins = <?php echo json_encode(get_option('active_plugins')); ?></script>

	<h2>Selective plugins loading</h2>
	
    <div id="v">
        <div><button v-on:click="addQuestion(0)">Add Filter</button></div>

        <div v-for="(filter,filterIndex) in filters">
            <fieldset>
             <legend> <span v-on:click="moveQuestion(filterIndex)">[{{filterIndex + 1}}] </span><label>enabled<input type="checkbox" v-model="filter.enabled"></label><input size=20 :placeholder="'filter '+(filterIndex+1)" v-model="filter.name"> <button v-on:click="deleteQuestion(filterIndex)">Delete</button>				</legend>
			<div style="display:inline-block;vertical-align:top">

			<div>use * before/after string to match as begins/ends with/contains anywhere</div>
            <div><label><span>REQUEST_URI</span> <textarea rows="2" v-on:keyup="expandTextarea" v-on:click="expandTextarea" v-model="filter.request_uri"></textarea></label></div>
            <div><label><span>QUERY_STRING</span><textarea rows="2" v-on:keyup="expandTextarea" v-on:click="expandTextarea" v-model="filter.query_string"></textarea></label></div>
			</div>
			<div style="display:inline-block">
            <div><span>Action</span>
				<label><input type="radio" value="all" v-model="filter.action">Load all of the plugins</label>
				<label><input type="radio" value="load" v-model="filter.action">Load only the following plugins</label>
				<label><input type="radio" value="unload" v-model="filter.action">Load all of the plugins except</label>
			</div>
<!--			<select :value="filter.plugins" multiple :size="plugins.length">
			  <option v-for="plugin in plugins" v-bind:value="plugin">{{ plugin }}</option>
			</select>
-->			<div>
			<div v-if="filter.action!=='all' && filter.action" v-for="plugin in plugins"><label><input type="checkbox" :id="plugin" :value="plugin" v-model="filter.plugins" >{{plugin}}</label></div>
<!--		  <input type="checkbox" id="jack" value="Jack" v-model="filter.checkedNames">
  <input type="checkbox" id="john" value="John" v-model="checkedNames">
  <input type="checkbox" id="mike" value="Mike" v-model="checkedNames">
-->
			</div>
			
            </div>
        </fieldset>
        <div><button v-on:click="addQuestion(filterIndex+1)">Add Filter</button></div>
        </div>

      <div class="wrap">
  
         <form method="post" action="options.php">
		<input style="width:100%" type="hidden" name="selective_plugin_loading" :value="JSONdata"/>
		 
            <?php
               settings_fields("section");
  
               do_settings_sections("demo");
                 
               submit_button(); 
            ?>
         </form>
      </div>		
    </div>