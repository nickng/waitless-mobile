var loc=document.getElementById("loc");
function getLocation() {
  if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition);
  } else {
    x.innerHTML="Geolocation is not supported by this browser.";
  }

}
function showPosition(position) {
  loc.value = position.coords.latitude+","+position.coords.longitude;
}

getLocation();
