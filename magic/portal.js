var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
  || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
  isMobile = true;
}

jQuery(document).ready(function() {
  window.new_inc = 0

  if ( 'list' === jsObject.parts.action ) {
    window.load_tree()
  } else if ( 'profile' === jsObject.parts.action ) {
    window.load_profile()
  } else if ( 'map' === jsObject.parts.action ) {
    window.load_basic_map()
  }

});

window.post_item = ( action, data ) => {
  jQuery('.loading-spinner').addClass('active')
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: action, parts: jsObject.parts, data: data }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .done(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
    .fail(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
}







/***********************************************************************
 *
 * Profile Section
 *
 **********************************************************************/
window.load_profile = () => {
  jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: 'get_profile', parts: jsObject.parts }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .done(function(data){
      console.log(data)
      window.write_profile( data )
      jQuery('.loading-spinner').removeClass('active')
    })
    .fail(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
}

window.post_profile = ( action, data ) => {
  return jQuery.ajax({
    type: "POST",
    data: JSON.stringify({ action: action, parts: jsObject.parts, data: data }),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    url: jsObject.root + jsObject.parts.root + '/v1/' + jsObject.parts.type,
    beforeSend: function (xhr) {
      xhr.setRequestHeader('X-WP-Nonce', jsObject.nonce )
    }
  })
    .fail(function(e) {
      console.log(e)
      jQuery('#error').html(e)
      jQuery('.loading-spinner').removeClass('active')
    })
}

window.write_profile = ( data ) => {
  let content = jQuery('#wrapper')

  let title = ''
  if ( typeof data.nickname === 'undefined' ){
    title = data.title
  } else {
    title = data.nickname
  }

  content.empty().html(
    `
    <div class="callout">
      <div class="grid-x" id="profile-form">
        <div class="cell">
            <div class="section-subheader">
               Community Name
            </div>
            <div class="input-group">
                <input type="text" placeholder="Name" id="title" data-key="${title}" value="${title}" class="dt-communication-channel input-group-field title" />
                <div class="input-group-button">
                     <div class="wrapper-field-spinner"><span class="loading-field-spinner title"></span></div>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="section-subheader">
               Milestones
            </div>
            <div class="small button-group" id="milestone_wrapper" style="display: inline-block"></div>
        </div>
        <!-- Email -->
        <div class="cell">
            <div class="section-subheader">
                Email
            </div>
            <div id="email-container"></div>
        </div>
        <!-- Phone -->
        <div class="cell">
            <div class="section-subheader">
                Phone
            </div>
            <div id="phone-container"></div>
        </div>
        <!-- location -->
        <div class="cell" id="mapbox-select">
            <div class="section-subheader">
               Location
            </div>
            <span id="location-label"></span>
            <div id="map-wrapper-edit">
                <div id='map-edit'></div>
            </div>
          <br>
          <button type="button"  style="display:none;" class="button primary-button-hollow alert small remove-location">Remove Location</button>
          <span class="loading-field-spinner location"></span>
        </div>
      </div>
    </div>

    <div class="callout">
      <div class="grid-x">
        <div class="cell">
            <h2>Security</h2>
        </div>
         <div class="cell">
            <div class="small button-group" id="restrictions_wrapper" style="display: inline-block"></div>
        </div>
      </div>
    </div>
   `)

  /* SETUP */
  /* milestones */
  if ( typeof jsObject.post_fields.leader_milestones !== 'undefined' ){
    let m_wrapper = jQuery('#milestone_wrapper')
    let m_class = ''
    jQuery.each(jsObject.post_fields.leader_milestones.default, function(i,v){
      m_class = 'empty-select-button'
      if ( typeof data.leader_milestones !== 'undefined' && findValueInArray(i,data.leader_milestones) ){
        m_class = 'selected-select-button'
      }
      m_wrapper.append(`
        <button id="${i}" type="button" data-field-key="leader_milestones" data-option-key="${i}" class="dt_multi_select ${m_class} select-button button">
          <img class="dt-icon" src="${v.icon}">
            ${v.label}
        </button>
      `)
    })
  }
  if ( typeof jsObject.post_fields.leader_community_restrictions !== 'undefined' ){
    let m_wrapper = jQuery('#restrictions_wrapper')
    let m_class = ''
    jQuery.each(jsObject.post_fields.leader_community_restrictions.default, function(i,v){
      m_class = 'empty-select-button'
      if ( typeof data.leader_community_restrictions !== 'undefined' && findValueInArray(i,data.leader_community_restrictions) ){
        m_class = 'selected-select-button'
      }
      m_wrapper.append(`
        <button id="${i}" type="button" data-field-key="leader_community_restrictions" data-option-key="${i}" class="dt_multi_select ${m_class} select-button button">
          <img class="dt-icon" src="${v.icon}">
            ${v.label}
        </button>
      `)
    })
  }
  /* phone */
  let phone_container = jQuery('#phone-container')
  let phone_value = ''
  if ( typeof data.contact_phone !== 'undefined' ){
    phone_value = data.contact_phone[0].value
  }
  phone_container.append(`
    <div class="input-group">
        <input id="" type="text" data-field="contact_phone" value="${phone_value}" class="dt-communication-channel input-group-field phone" dir="auto">
        <div class="input-group-button">
            <div class="wrapper-field-spinner"><span class="loading-field-spinner phone"></span></div>
        </div>
    </div>
  `)
  /* email */
  let email_container = jQuery('#email-container')
  let email_value = ''
  if ( typeof data.contact_email !== 'undefined' ){
    email_value = data.contact_email[0].value
  }
  email_container.append(`
    <div class="input-group">
        <input id="" type="text" data-field="contact_phone" value="${email_value}" class="dt-communication-channel input-group-field email" dir="auto">
        <div class="input-group-button">
            <div class="wrapper-field-spinner"><span class="loading-field-spinner email"></span></div>
        </div>
    </div>
  `)
  /* location */
  let location = {
    lng: '',
    lat: '',
    label: '',
    grid_meta_id: ''
  }
  if ( typeof data.location_grid_meta !== 'undefined' ){
    location = data.location_grid_meta[0]
    jQuery('#location-label').html(data.location_grid_meta[0].label)
    jQuery('.remove-location').show().on('click', function(){
      console.log('remove')
      remove_location( data.ID, 'contacts' )
    })
  }
  window.load_mapbox( 'contacts', true, location.lng, location.lat, data.ID )


  /* LISTENERS */
  jQuery('.dt-communication-channel.input-group-field.title').on('change', function(e){
    jQuery('.loading-field-spinner.title').addClass('active')
    window.post_profile('update_profile_title', { post_id: data.ID, new_value: e.target.value } )
      .done(function(result) {
        console.log(result)
        if ( typeof result.errors !== 'undefined') {
          console.log(result)
        }
        jQuery('.loading-field-spinner.title').removeClass('active')
      })
  })
  jQuery('.dt_multi_select').on('click', function(e){
    // jQuery('.loading-field-spinner.title').addClass('active')
    let key = jQuery(this).data('field-key')
    let option = jQuery(this).data('option-key')
    let state = jQuery(this).hasClass('selected-select-button')

    if ( state ) {
      jQuery(this).removeClass('selected-select-button')
      jQuery(this).addClass('empty-select-button')
    } else {
      jQuery(this).addClass('selected-select-button')
      jQuery(this).removeClass('empty-select-button')
    }
    window.post_profile('update_multiselect', { post_id: data.ID, key: key, option: option, state: state } )
      .done(function(result) {
        console.log(result)
        if ( typeof result.errors !== 'undefined') {
          console.log(result)
        }
        jQuery('.loading-field-spinner.title').removeClass('active')
      })
  })
  jQuery('.dt-communication-channel.input-group-field.email').on('change', function(e){
    jQuery('.loading-field-spinner.email').addClass('active')
    window.post_profile('update_profile_email', { post_id: data.ID, new_value: e.target.value } )
      .done(function(result) {
        console.log(result)
        if ( typeof result.errors !== 'undefined') {
          console.log(result)
        }
        jQuery('.loading-field-spinner.email').removeClass('active')
      })
  })
  jQuery('.dt-communication-channel.input-group-field.phone').on('change', function(e){
    jQuery('.loading-field-spinner.phone').addClass('active')
    window.post_profile('update_profile_phone', { post_id: data.ID, new_value: e.target.value } )
      .done(function(result) {
        console.log(result)
        if ( typeof result.errors !== 'undefined') {
          console.log(result)
        }
        jQuery('.loading-field-spinner.phone').removeClass('active')
      })
  })

}

function findValueInArray(value,arr){
  var result = false;

  for(var i=0; i<arr.length; i++){
    var name = arr[i];
    if(name === value){
      result = true;
      break;
    }
  }

  return result;
}
















/*************************************************************************
 *
 * Location
 *
 ************************************************************************/

window.load_mapbox = ( post_type, save_immediately, lng, lat, post_id ) => {

  let center, zoom
  if ( lng ) {
    center = [lng, lat]
    zoom = 5
  } else {
    center = [-20, 30]
    zoom = 1
  }

  /***********************************
   * Map
   ***********************************/
  mapboxgl.accessToken = jsObject.map_key;
  var map = new mapboxgl.Map({
    container: 'map-edit',
    style: 'mapbox://styles/mapbox/light-v10',
    center: center,
    zoom: zoom
  });

  window.force_values = false
  if ( lng ) {
    let marker_center = new mapboxgl.LngLat(lng, lat)
    window.active_marker = new mapboxgl.Marker()
      .setLngLat(marker_center)
      .addTo(map);
    map.flyTo({
      center: center,
      zoom: 12,
      bearing: 0,
      speed: 2, // make the flying slow
      curve: 1, // change the speed at which it zooms out
      easing: (t) => t,
      essential: true
    });
    window.force_values = true // wipe out previous location data on the record
    jQuery('.remove-location').show() // show the removal button
  }


  /***********************************
   * Click
   ***********************************/
  map.on('click', function (e) {
    console.log(e)

    let lng = e.lngLat.lng
    let lat = e.lngLat.lat
    window.active_lnglat = [lng,lat]

    // add marker
    if ( window.active_marker ) {
      window.active_marker.remove()
    }
    window.active_marker = new mapboxgl.Marker()
      .setLngLat(e.lngLat )
      .addTo(map);

    jQuery('#location-label').empty()
    jQuery('.remove-location').hide()

    window.location_data = {
      location_grid_meta: {
        values: [
          {
            lng: lng,
            lat: lat,
            source: 'user'
          }
        ],
        force_values: window.force_values
      }
    }
    if ( save_immediately ) {
      save_new_location( post_id, post_type )
    }

  });

  /***********************************
   * Search
   ***********************************/
  var geocoder = new MapboxGeocoder({
    accessToken: mapboxgl.accessToken,
    types: 'country region district locality neighborhood address place',
    mapboxgl: mapboxgl
  });
  map.addControl(geocoder, 'top-left' );
  geocoder.on('result', function(e) { // respond to search
    console.log(e)
    if ( window.active_marker ) {
      window.active_marker.remove()
    }
    window.active_marker = new mapboxgl.Marker()
      .setLngLat(e.result.center)
      .addTo(map);
    geocoder._removeMarker()

    jQuery('#location-label').html(e.result.place_name)
    jQuery('.remove-location').hide()

    window.location_data = {
      location_grid_meta: {
        values: [
          {
            lng: e.result.center[0],
            lat: e.result.center[1],
            level: e.result.place_type[0],
            label: e.result.place_name,
            source: 'user'
          }
        ],
        force_values: window.force_values
      }
    }

    if ( save_immediately ) {
      save_new_location( post_id, post_type )
    }
  })

  /***********************************
   * Geolocate Browser
   ***********************************/
  let userGeocode = new mapboxgl.GeolocateControl({
    positionOptions: {
      enableHighAccuracy: true
    },
    marker: {
      color: 'orange'
    },
    trackUserLocation: false,
    showUserLocation: false
  })
  map.addControl(userGeocode, 'top-left' );
  userGeocode.on('geolocate', function(e) { // respond to search
    console.log(e)
    if ( window.active_marker ) {
      window.active_marker.remove()
    }

    let lat = e.coords.latitude
    let lng = e.coords.longitude

    window.active_lnglat = [lng,lat]
    window.active_marker = new mapboxgl.Marker()
      .setLngLat([lng,lat])
      .addTo(map);

    jQuery('#location-label').empty()
    jQuery('.remove-location').hide()

    window.location_data = {
      location_grid_meta: {
        values: [
          {
            lng: lng,
            lat: lat,
            source: 'user'
          }
        ],
        force_values: window.force_values
      }
    }

    if ( save_immediately ) {
      save_new_location( post_id, post_type )
    }
  })

  let navControl = new mapboxgl.NavigationControl();
  map.addControl( navControl, 'top-left' );
  map.touchZoomRotate.disableRotation();
  map.dragRotate.disable();

}

function activate_geolocation() {
  jQuery(".mapboxgl-ctrl-geolocate").click();
}

function save_new_location( post_id, post_type = 'groups' ) {
  if ( typeof window.location_data === undefined || window.location_data === false ) {
    jQuery('#result_display').html(`You haven't selected anything yet. Click, search, or allow auto location.`)
    return;
  }
  jQuery('.loading-field-spinner.location').addClass('active')

  console.log(window.location_data)
  window.post_item('update_location', {
    post_id: post_id,
    post_type: post_type,
    fields: window.location_data,
    delete: window.force_values
  })
    .done(function (result) {
      console.log(result)
      jQuery('#location-label').html(result.location_grid_meta[0].label)
      jQuery('.remove-location').show();
      jQuery('.loading-field-spinner.location').removeClass('active')
      window.force_values = true

      // reload flat map
      if ( 'map' === jsObject.parts.action || 'goals_map' === jsObject.parts.action ) {
        window.get_grid_data('grid_data', 0)
          .done(function (x) {
            jsObject.grid_data = x
          })
      }
    })
}

function remove_location( post_id, post_type = 'groups' ) {
  jQuery('.loading-field-spinner.location').addClass('active')
  window.post_item('delete_location', { post_id: post_id, post_type: post_type, fields: {} } )
    .done(function(result) {
      console.log(result)
      jQuery('.remove-location').hide();
      jQuery('.loading-field-spinner.location').removeClass('active')
      window.force_values = false
      window.location_data = {}
      jQuery('#location-label').empty()
      window.load_mapbox(post_type, ( 'contacts' ===  post_type ), null, null, post_id )
    })
}







/*************************************************************************
 *
 * New Group Modal
 *
 ************************************************************************/

window.open_create_modal = ( parent_id ) => {
  let title = jQuery('#modal-title')
  let content = jQuery('#modal-content')

  title.empty().html(`<h1>Add New Church</h1>`)
  content.empty().html(`
    <div class="grid-x" id="church-modal">

      <!-- title -->
      <div class="cell">
        <div class="section-subheader">
           Name <span style="color:red;">*</span>
        </div>
        <div class="input-group">
          <input id="create-title" type="text"  placeholder="" value="" autofocus />
          <div class="input-group-button">
               <div><span class="loading-field-spinner group_title"></span></div>
          </div>
        </div>
      </div>

      <!-- start date -->
      <div class="cell">
        <div class="section-subheader">
           Start Date <span style="color:red;">*</span>
        </div>
        <div class="input-group">
          <input id="create-date" type="date" value="" />
          <div class="input-group-button">
               <div><span class="loading-field-spinner group_start_date"></span></div>
          </div>
        </div>
      </div>

      <!-- members -->
      <div class="cell">
       <div class="section-subheader">
           Number of Members <span style="color:red;">*</span>
        </div>
        <div class="input-group">
          <input id="create-members" type="number"  value="0" />
          <div class="input-group-button">
               <div><span class="loading-field-spinner group_member_count"></span></div>
          </div>
        </div>
      </div>

      <!-- location -->
      <div class="cell">
          <div class="section-subheader">
             Location <span style="color:red;">*</span>
          </div>
          <span id="location-label"></span>
          <div id="map-wrapper-edit">
              <div id='map-edit'></div>
          </div>
        <br>
        <button type="button"  style="display:none;" class="button primary-button-hollow alert small remove-location">Remove Location</button>
        <span class="loading-field-spinner location"></span>
      </div>

      <!-- parent -->
      <div class="cell" id="parent-cell">
       <div class="section-subheader">
           Parent Church
        </div>
        <div>
            <select id="create-parent">
                <option value="none"></option>
            </select>
        </div>
      </div>

        <!-- submit -->
      <div class="cell" id="map-action-buttons">
          <button type="button" class="button" id="create-church">Create Church</button> <span class="loading-spinner"></span> <span id="error"></span>
      </div>
    </div>
  `)

  jQuery('#edit-modal').foundation('open')

  jQuery('#create-church').on('click', function(e){
    let button = jQuery('#create-church')
    button.prop('disabled', true )
    let title = jQuery('#create-title').val()
    let start_date = jQuery('#create-date').val()
    let members = jQuery('#create-members').val()
    let parent = jQuery('#create-parent').val()

    if ( typeof window.location_data === 'undefined' || window.location_data === '' ) {
      jQuery('#location-label').html('<span style="color:red;">Must add a location for the church.</span>')
      jQuery('#create-church').prop('disabled', false )
      return
    }

    let data = {
      name: title,
      start_date: start_date,
      members: members,
      location_grid_meta: window.location_data.location_grid_meta,
      parent: parent
    }

    window.post_item('create_church', data )
      .done(function(result) {
        if ( result ) {
          jQuery('#modal-title').empty()
          jQuery('#modal-content').empty()
          jQuery('#edit-modal').foundation('close')

          jsObject.post = result.contact_post

          // reload current page
          if ( 'goals_map' === jsObject.parts.action ) {
            jsObject.custom_marks = result.custom_marks
            load_map()
            jQuery('#offCanvasNestedPush').foundation('close')
          }
          else if ( 'map' === jsObject.parts.action ) {
            jsObject.custom_marks = result.custom_marks
            window.load_basic_map()
          }
          else if ( 'list' === jsObject.parts.action ) {
            window.load_tree()
          }
        }
      })

  })

  window.load_mapbox( 'groups' )

  if ( typeof jsObject.post.reporter !== 'undefined' ) {
    let key_select = jQuery('#create-parent')
    let selected_attr
    jQuery.each( jsObject.post.reporter, function(i,v) {
      selected_attr = ''
      if ( parseInt(parent_id) === parseInt(v.ID) ) {
        selected_attr = 'selected'
      }
      key_select.append(`<option value="${v.ID}" ${selected_attr}>${v.post_title}</option>`)
    })
  }

}
jQuery('.float').on('click', function(){
  window.open_create_modal()
})









/*************************************************************************
 *
 * List Section
 *
 ************************************************************************/
window.load_tree = () => {
  jQuery('#wrapper').html(`
    <div class="dd" id="domenu-0">
        <button class="dd-new-item" style="font-weight:300;font-size: 1.25rem;display:none;"><i class="fi-plus"></i> ADD NEW CHURCH</button>
        <li class="dd-item-blueprint" id="" data-prev_parent="domenu-0">
            <button class="collapse" data-action="collapse" type="button" style="display: none;">–</button>
            <button class="expand" data-action="expand" type="button" style="display: none;">+</button>
            <div class="dd-handle dd3-handle">&nbsp;</div>
            <div class="dd3-content">
                <div class="item-name">[item_name]</div>
                <div class="dd-button-container">
                    <button class="item-edit">✎</button>
                    <button class="item-add-child">+</button>
                    <button class="item-remove" style="display:none;">&times;</button>
                </div>
                <div class="dd-edit-box" style="display: none;">
                    <input type="text" name="title" autocomplete="off" placeholder="Item"
                           data-placeholder="Any nice idea for the title?"
                           data-default-value="Saving New Church {?numeric.increment}">
                </div>
            </div>
        </li>
        <ol class="dd-list"></ol>
    </div>
  `)
  window.post_item( 'load_tree', {} )
    .done(function(data){
      window.load_domenu(data)
      jQuery('#initial-loading-spinner').hide()
    })
}

window.load_domenu = ( data ) => {

  jQuery('#domenu-0').domenu({
    data: JSON.stringify( data.tree ),
    maxDepth: 500,
    refuseConfirmDelay: 500, // does not delete immediately but requires a second click to confirm.
    select2:                {
      support:     false, // Enable Select2 support
    }
  }).parseJson()

    .onCreateItem(function(e) {
      console.log('onCreateItem')

      // e.attr('style', 'display:none;')

    })
    .onItemAddChildItem(function(e) {
      console.log('onItemAddChildItem')
      console.log( e[0].id )

      // window.open_create_modal( e[0].id )

    })
    .onItemRemoved(function(e) {
      if ( window.last_removed !== e[0].id ) {
        console.log('onItemRemoved')
        jQuery('.loading-spinner').addClass('active')

        window.last_removed = e[0].id

        window.post_item('onItemRemoved', { id: e[0].id } ).done(function(removed_id){
          if ( removed_id ) {
            console.log('success onItemRemoved')
            jQuery.each( jsObject.post.reporter, function (i,v) {
              if ( v.ID === removed_id ) {
                jsObject.post.reporter.splice(i, 1)
              }
            })
          }
          else {
            console.log('did not remove item')
          }
          jQuery('.loading-spinner').removeClass('active')
        })
      }
    })
    .onItemDrop(function(e) {
      if ( typeof e.prevObject !== 'undefined' && typeof e[0].id !== 'undefined' ) { // runs twice on drop. with and without prevObject
        console.log('onItemDrop')
        jQuery('.loading-spinner').addClass('active')

        let new_parent = e[0].parentNode.parentNode.id
        let self = e[0].id

        // console.log(' - new parent: '+ new_parent)
        // console.log(' - self: '+ self)

        let prev_parent_object = jQuery('#'+e[0].id)
        let previous_parent = prev_parent_object.data('prev_parent')

        prev_parent_object.attr('data-prev_parent', new_parent ) // set previous

        if ( new_parent !== previous_parent ) {
          window.post_item('onItemDrop', { new_parent: new_parent, self: self, previous_parent: previous_parent } ).done(function(drop_data){
            jQuery('.loading-spinner').removeClass('active')
            if ( drop_data ) {
              console.log('success onItemDrop')
            }
            else {
              console.log('did not edit item')
            }
          })
        }

      }
    })
    .onItemSetParent(function(e) {
      if (typeof e[0] !== 'undefined' ) {
        console.log('onItemSetParent')
        console.log(' - has children: ' + e[0].id)
        jQuery('#' + e[0].id + ' button.item-remove:first').hide();
      }
    })
    .onItemUnsetParent(function(e) {
      if (typeof e[0] !== 'undefined' ) {
        console.log('onItemUnsetParent')
        console.log(' - has no children: '+ e[0].id)
        jQuery('#' + e[0].id + ' button.item-remove:first').show();
      }
    })


  // list prep
  jQuery.each( jQuery('#domenu-0 .item-name'), function(i,v){
    // move and set the title to id
    jQuery(this).parent().parent().attr('id', jQuery(this).html())
  })
  // set the previous parent data element
  jQuery.each( data.parent_list, function(ii,vv) {
    if ( vv !== null && vv !== "undefined") {
      let target = jQuery('#'+ii)
      if ( target.length > 0 ) {
        target.attr('data-prev_parent', vv )
      }
    }
  })
  // show delete for last item
  jQuery("li:not(:has(>ol)) .item-remove").show()
  // set title
  jQuery.each(jQuery('.item-name'), function(ix,vx) {
    let old_title = jQuery(this).html()
    jQuery(this).html(data.title_list[old_title])
  })
  // set listener for edit button
  jQuery('#domenu-0 .item-edit').on('click', function(e) {
    window.open_edit_modal(e.currentTarget.parentNode.parentNode.parentNode.id)
  })
  jQuery('#domenu-0 .item-add-child').on('click', function(e) {
    window.open_create_modal(e.currentTarget.parentNode.parentNode.parentNode.id)
  })
}

window.open_edit_modal = ( group_id ) => {
  window.open_empty_modal()

  window.post_item('get_group', { post_id: group_id } )
    .done( function( data ) {

      // load
      window.open_create_modal( )

      // fill
      jQuery('#modal-title').html(`<h1>Edit Church</h1>`)
      jQuery('#map-action-buttons').html(`
        <button type="button" class="button" id="edit-church">Save Church Edits</button> <span class="loading-spinner"></span>
      `)
      if ( typeof data.post.title !== 'undefined' ) {
        jQuery('#create-title').val( data.post.title )
      }
      if ( typeof data.post.church_start_date !== 'undefined' ) {
        jQuery('#create-date').val( data.post.church_start_date.formatted )
      }
      if ( typeof data.post.member_count !== 'undefined' ) {
        jQuery('#create-members').val(data.post.member_count )
      }
      window.location_data = {}
      if ( typeof data.post.location_grid_meta !== 'undefined' ){
        window.location_data = {
          location_grid_meta: {
            values: [
              {
                lng: data.post.location_grid_meta[0].lng,
                lat: data.post.location_grid_meta[0].lat,
                level: data.post.location_grid_meta[0].level,
                label: data.post.location_grid_meta[0].label,
                source: 'user'
              }
            ],
            force_values: window.force_values
          }
        }
        jQuery('#location-label').html(data.post.location_grid_meta[0].label)
        jQuery('.remove-location').show().on('click', function(){
          console.log('remove')
          window.location_data = {}
          jQuery('#location-label').empty()
          jQuery('.remove-location').hide()
          window.load_mapbox( 'groups', false, null, null, data.post.ID )
        })
        window.load_mapbox( 'groups', false, data.post.location_grid_meta[0].lng, data.post.location_grid_meta[0].lat, data.post.ID )
      }
      else {
        window.load_mapbox( 'groups', false, null, null, data.post.ID )
      }

      jQuery('#parent-cell').empty()

      // listener
      jQuery('#edit-church').on('click', function(e) {
        jQuery('#edit-church').prop('disabled', true )
        let title = jQuery('#create-title').val()
        let start_date = jQuery('#create-date').val()
        let members = jQuery('#create-members').val()
        let group_status = 'active' // jQuery('#create-group-status').val()

        if ( typeof window.location_data === 'undefined' || typeof window.location_data.location_grid_meta === 'undefined' ) {
          jQuery('#location-label').html('<span style="color:red;">Must add a location for the church.</span>')
          jQuery('#edit-church').prop('disabled', false )
          return
        }

        let fields = {
          title: title,
          group_status: group_status,
          church_start_date: start_date,
          member_count: members,
          location_grid_meta: window.location_data.location_grid_meta,
        }

        window.post_item('update_church', { post_id: data.post.ID, fields: fields } )
          .done(function(result) {
            if ( result ) {
              jQuery('#modal-title').empty()
              jQuery('#modal-content').empty()
              jQuery('#edit-modal').foundation('close')

              jsObject.post = result

              // reload current page
              if ( 'goals_map' === jsObject.parts.action ) {
                jsObject.custom_marks = result.custom_marks
                load_map()
                jQuery('#offCanvasNestedPush').foundation('close')
              }
              else if ( 'map' === jsObject.parts.action ) {
                window.load_basic_map()
              }
              else if ( 'list' === jsObject.parts.action ) {
                window.load_tree()
              }
            }
          })
      })

    })
    .fail(function(e) {
      jQuery('#modal-content').html(`Sorry. No group found. Refresh the page and try again.`)
    })

}

window.open_empty_modal = () => {
  let title = jQuery('#modal-title')
  let content = jQuery('#modal-content')

  title.empty().html(`<span class="loading-spinner active"></span>`)
  content.empty()
  jQuery('#edit-modal').foundation('open')
}




/*************************************************************************
 *
 * Simple Map Section
 *
 ************************************************************************/
window.load_basic_map = () => {

  window.activity_list = {}
  window.activity_geojson = {
    "type": "FeatureCollection",
    "features": []
  }

  // Add html and map
  let map_height = window.innerHeight - 65
  if ( isMobile && window.innerWidth < 640 ) {
    map_height = window.innerHeight / 2
  }
  jQuery('#custom-map-style').append(`
      <style>
          #church-list-wrapper {
              height: ${window.innerHeight - 130}px !important;
              overflow: scroll;
              padding-right:1rem;
              padding-left: 1rem;
          }

          #church-list-wrapper .callout {
             border-radius: 10px;
          }
          #church-list-wrapper h2 {
              font-size:1.2em;
              font-weight:bold;
          }
          #map-wrapper {
              height: ${map_height}px !important;
          }
          #map {
              height: ${map_height}px !important;
          }
           #map-header {
                position: absolute;
                top:10px;
                left:10px;
                z-index: 20;
                background-color: white;
                padding:1em;
                opacity: 0.8;
                border-radius: 5px;
            }
      </style>
  `)

  mapboxgl.accessToken = jsObject.map_key;
  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/light-v10',
    center: [-98, 38.88],
    minZoom: 1,
    maxZoom: 15,
    zoom: 5
  });

  // disable map rotation using right click + drag
  map.dragRotate.disable();
  map.touchZoomRotate.disableRotation();

  if ( ! ( isMobile && window.innerWidth < 640 ) ) {
    map.addControl(
      new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl
      })
    );
  }

  map.on('load', function() {
    initialize_cluster_map()
  });

  map.on('zoomend', function(e){
    write_list( map.queryRenderedFeatures() )
  })
  map.on('dragend', function(e){
    write_list( map.queryRenderedFeatures() )
  })


  function initialize_cluster_map() {
    map.addSource('layer-source-basic-map', {
      type: 'geojson',
      data: window.activity_geojson,
      cluster: true,
      clusterMaxZoom: 7,
      clusterRadius: 50
    });
    map.addLayer({
      id: 'clusters',
      type: 'circle',
      source: 'layer-source-basic-map',
      filter: ['has', 'point_count'],
      paint: {
        'circle-color': [
          'step',
          ['get', 'point_count'],
          '#00d9ff',
          20,
          '#00aeff',
          150,
          '#90C741'
        ],
        'circle-radius': [
          'step',
          ['get', 'point_count'],
          20,
          100,
          30,
          750,
          40
        ]
      }
    });
    map.addLayer({
      id: 'cluster-count-basic-map',
      type: 'symbol',
      source: 'layer-source-basic-map',
      filter: ['has', 'point_count'],
      layout: {
        'text-field': '{point_count_abbreviated}',
        'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
        'text-size': 12
      }
    });
    map.addLayer({
      id: 'unclustered-point-basic-map',
      type: 'circle',
      source: 'layer-source-basic-map',
      filter: ['!', ['has', 'point_count']],
      paint: {
        'circle-color': '#00d9ff',
        'circle-radius':12,
        'circle-stroke-width': 1,
        'circle-stroke-color': '#fff'
      }
    });


    window.post_item('get_geojson', {} )
      .done( data => {
        "use strict";
        window.activity_geojson = data

        var mapSource= map.getSource('layer-source-basic-map');
        if( typeof mapSource !== 'undefined') {
          map.getSource('layer-source-basic-map').setData(window.activity_geojson);
        }

        var bounds = new mapboxgl.LngLatBounds();
        window.activity_geojson.features.forEach(function(feature) {
          bounds.extend(feature.geometry.coordinates);
        });
        map.fitBounds(bounds, { padding: {top: 20, bottom:20, left: 20, right: 20 } });

      })
  }

  function write_list( features ) {
    let wrapper = jQuery('#church-list-wrapper')
    wrapper.empty()
    jQuery.each( features, function(i,v){
      if ( v.source === 'layer-source-basic-map' && v.layer.id !== 'cluster-count-basic-map' && v.layer.id !== 'clusters' ) {
        wrapper.append(`
          <div class="callout">
              <h2>${v.properties.title} <i class="fi-pencil" onclick="window.open_edit_modal(${v.properties.ID})" style="cursor:pointer; float:right;"></i> </h2>
              <div>Church Start: ${v.properties.church_start_date}</div>
              <div>Members: ${v.properties.member_count}</div>
              <div>Location: ${v.properties.location_title}</div>
              <div>Parent: ${v.properties.parent_title}</div>
          </div>
        `)
      }
      if ( v.source === 'layer-source-basic-map'  && v.layer.id === 'cluster-count-basic-map' ) {
        wrapper.append(`
          <div class="callout">
              <h2>Cluster of ${v.properties.point_count_abbreviated}</h2>
          </div>
        `)
      }
    })
  }


}
