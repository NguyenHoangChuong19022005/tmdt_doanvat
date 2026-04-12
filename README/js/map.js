/* ========= CẤU HÌNH ========= */
const MAPBOX_TOKEN = ""; // Có thì dán 'pk....' để gợi ý chuẩn; bỏ trống dùng Photon/Nominatim
const OSRM_BASE    = "https://router.project-osrm.org/route/v1";
const VN_VIEWBOX   = [102.14441, 8.17907, 109.469, 23.392];
const HCMC_CENTER  = [10.776889, 106.700806]; // lat, lon
const HCMC_PIVOT   = [106.700806, 10.776889]; // lon, lat
const HN_CENTER    = [21.028511, 105.852003];
const DN_CENTER    = [16.0610, 108.2270];

const COLORS = { low:'#2ecc71', med:'#f39c12', high:'#e74c3c' };

/* ========= CHI NHÁNH ========= */
const BRANCHES = [
  { id:1,  name:"Hồ Hoàn Kiếm",            address:"Hoàn Kiếm, Hà Nội",                 lat:21.028511, lng:105.852003 },
  { id:2,  name:"Công viên Thống Nhất",     address:"Hai Bà Trưng, Hà Nội",             lat:21.016970, lng:105.840590 },
  { id:3,  name:"Bờ Hồ Tây",                address:"Tây Hồ, Hà Nội",                   lat:21.057000, lng:105.822000 },
  { id:4,  name:"Phố đi bộ Nguyễn Huệ",     address:"Quận 1, TP.HCM",                   lat:10.775600, lng:106.703300 },
  { id:5,  name:"Công viên Tao Đàn",        address:"Quận 1, TP.HCM",                   lat:10.774300, lng:106.692300 },
  { id:6,  name:"Bãi biển Mỹ Khê",          address:"Sơn Trà, Đà Nẵng",                 lat:16.061400, lng:108.243000 },
  { id:7,  name:"Cầu Rồng",                 address:"Hải Châu, Đà Nẵng",                lat:16.061000, lng:108.227000 },
  { id:8,  name:"Bãi biển Nha Trang",       address:"Trần Phú, Nha Trang",              lat:12.238791, lng:109.196749 },
  { id:9,  name:"Công viên 23/10",          address:"Nha Trang, Khánh Hòa",             lat:12.259000, lng:109.178000 },
  { id:10, name:"Bãi biển Vũng Tàu",        address:"TP. Vũng Tàu, Bà Rịa–Vũng Tàu",    lat:10.346000, lng:107.084000 },
  { id:11, name: "CN Bến Thành",            address: "Chợ Bến Thành, Q1",               lat:10.772,  lng:106.698   },
  { id:12, name: "CN Đức Bà",               address: "Nhà thờ Đức Bà, Q1",              lat:10.7798, lng:106.699   },
  { id:13, name: "CN Landmark 81",          address: "Landmark 81, Bình Thạnh",         lat:10.7959, lng:106.7218  },
  { id:14, name: "CN Thảo Điền",            address: "Thảo Điền, TP Thủ Đức",           lat:10.8028, lng:106.7354  },
  { id:15, name: "CN Chợ Lớn",              address: "Q5 (Chợ Lớn)",                    lat:10.7525, lng:106.6675  },
  { id:16, name: "GTVT",                    address: "ĐH GTVT",                         lat:10.867553337027202, lng:106.61671464779471 },
  { id:17, name: "CN Thủ Đức",              address: "Đường Võ Văn Ngân, Thủ Đức",      lat:10.869,  lng:106.762   },
];

/* ========= ICON & STATE ========= */
const RedIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34], shadowSize:[41,41]
});
let map, baseLayers={};
let userMarker = null;  // vị trí hiện tại
let markerB = null;     // điểm đến
let nameB   = '';
let routeLayer = null;  // featureGroup các đoạn tuyến tô màu
let branchMarkers = [];
let selectingB = false;

/* ========= TIỆN ÍCH ========= */
const $ = s => document.querySelector(s);
function showStatus(t,ms=2500){const el=$('#status');if(!el)return;el.textContent=t;el.style.display='block';clearTimeout(showStatus._t);showStatus._t=setTimeout(()=>el.style.display='none',ms);}
function updateETA(mins){ const el=$('#eta'); if(el) el.textContent = mins ? `${Math.round(mins)} phút` : '—'; }
function setTrafficText(txt){ const el=$('#traffic'); if(el) el.textContent = txt || '—'; }
function setWeatherText(txt){ const el=$('#weather'); if(el) el.textContent = txt || '—'; }
function setNotes(html){ const el=$('#notes'); if(el) el.innerHTML = html || ''; }
function updateGoBState(){
  const typed = ($('#searchB')?.value.trim().length >= 3);
  const btn = $('#goB');
  if(btn) btn.disabled = !(markerB || typed);
}
function hhmm(d){ return d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}); }
function updateBranchHeaderCount(n){
  const sp = document.getElementById('branch-count');
  if (sp) { sp.textContent = n; return; }
  const h3 = document.querySelector('.sidebar-head h3, .sidebar h3, h3');
  if (h3) h3.textContent = h3.textContent.replace(/\(\d+\)/, `(${n})`);
}

/* ========= Toán/Địa lý ========= */
function distKm(aLat,aLng,bLat,bLng){
  const R=6371, rad=d=>d*Math.PI/180;
  const dLat=rad(bLat-aLat), dLng=rad(bLng-aLng);
  const A=Math.sin(dLat/2)**2 + Math.cos(rad(aLat))*Math.cos(rad(bLat))*Math.sin(dLng/2)**2;
  return 2*R*Math.asin(Math.sqrt(A));
}
function clamp(x,a=0,b=1){return Math.max(a,Math.min(b,x));}
function gauss(x,mu,sig){return Math.exp(-0.5*((x-mu)/sig)**2);}
function nearestCityFactor(lat,lng){
  const d = Math.min(
    distKm(lat,lng,HCMC_CENTER[0],HCMC_CENTER[1]),
    distKm(lat,lng,HN_CENTER[0],HN_CENTER[1]),
    distKm(lat,lng,DN_CENTER[0],DN_CENTER[1])
  );
  const f = Math.exp(-d/7); // mạnh trong ~7 km nội đô
  return clamp(0.3 + 0.7*f, 0, 1);
}

/* ========= AI traffic heuristic ========= */
function trafficBaseScore(dateLocal){
  const h = dateLocal.getHours();
  const dow = dateLocal.getDay(); // 0=CN
  const weekday = (dow>=1 && dow<=5);
  let base;
  if(weekday){
    base = 0.75*gauss(h, 8, 1.4) + 0.9*gauss(h, 18, 1.6) + 0.35*gauss(h, 12, 1.4);
  }else{
    base = 0.25*gauss(h, 11, 2.2) + 0.45*gauss(h, 18, 1.8);
  }
  return clamp(base);
}
function trafficLevel(score){
  if(score < 0.33) return 'low';
  if(score < 0.66) return 'med';
  return 'high';
}
function predictTrafficWindow(repLatLng, depart, durationSec){
  const repF = nearestCityFactor(repLatLng[0], repLatLng[1]);
  const windowSec = durationSec + 120*60; // +2h dự phòng
  const step = 10*60;
  const runs = [];
  let cur = null;

  for(let t=0; t<=windowSec; t+=step){
    const dt = new Date(depart.getTime() + t*1000);
    const base = trafficBaseScore(dt);
    const s = clamp(0.2 + 0.8*base*repF);
    const lv = trafficLevel(s);
    if(!cur) cur = {lv, t0: dt, t1: dt};
    else if(cur.lv === lv){ cur.t1 = dt; }
    else { runs.push(cur); cur = {lv, t0: dt, t1: dt}; }
  }
  if(cur) runs.push(cur);

  const high = runs.find(r=>r.lv==='high');
  if(high) return {lv:'high', t0:high.t0, t1:new Date(high.t1.getTime()+step*1000)};
  const med  = runs.find(r=>r.lv==='med');
  if(med)  return {lv:'med', t0:med.t0,  t1:new Date(med.t1.getTime()+step*1000)};
  const low  = runs.find(r=>r.lv==='low');
  return low ? {lv:'low', t0:low.t0, t1:new Date(low.t1.getTime()+step*1000)} : null;
}

/* ========= Weather (Open-Meteo) ========= */
async function fetchWeather(lat, lng){
  const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&hourly=precipitation_probability,precipitation,cloudcover,windspeed_10m&forecast_days=2&timezone=auto`;
  const r = await fetch(url); if(!r.ok) throw new Error('Open-Meteo lỗi'); return r.json();
}
function pickHourIndex(hourArrISO, targetDt){
  const t = targetDt.toISOString().slice(0,13); // YYYY-MM-DDTHH
  let i = hourArrISO.findIndex(s => s.startsWith(t));
  if(i === -1){
    const td = targetDt.getTime();
    let best=0,bd=Infinity;
    hourArrISO.forEach((s,idx)=>{const d=Math.abs(new Date(s).getTime()-td); if(d<bd){bd=d;best=idx;}});
    i=best;
  }
  return i;
}
function weatherRisk(pp, wind){ // 0..1
  const p = clamp(pp/100);
  const w = clamp((wind-15)/25);
  return clamp(0.6*p + 0.4*w);
}
function riskLevel(x){ return x<0.33?'low': (x<0.66?'med':'high'); }

/* ========= BASEMAPS & KHỞI TẠO ========= */
function initBasemaps(){
  const osm=L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap'});
  const cartoLight=L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',{attribution:'&copy; OpenStreetMap, &copy; CARTO',subdomains:'abcd',maxZoom:20});
  const cartoDark=L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',{attribution:'&copy; OpenStreetMap, &copy; CARTO',subdomains:'abcd',maxZoom:20});
  baseLayers={osm,cartoLight,cartoDark}; cartoLight.addTo(map);
}
function switchBasemap(name){
  Object.values(baseLayers).forEach(l=>{try{map.removeLayer(l);}catch{}});
  baseLayers[name].addTo(map);
}

function init(){
  map=L.map('map',{center:[10.78,106.7],zoom:12});
  initBasemaps(); renderBranches(BRANCHES); updateBranchHeaderCount(BRANCHES.length);

  // click map để đặt B
  map.on('click', e=>{
    if(!selectingB) return;
    const {lat,lng}=e.latlng;
    setB([lat,lng], 'Điểm B (click bản đồ)');
    showStatus('Đã đặt B tại vị trí đã click');
    selectingB=false; $('#pickB')?.classList.remove('active');
  });

  $('#fit-all')?.addEventListener('click', fitAll);
  $('#basemap-select')?.addEventListener('change',e=>switchBasemap(e.target.value));
  $('#now')?.addEventListener('click',()=>{
    const t=new Date(); t.setMinutes(t.getMinutes()-t.getTimezoneOffset());
    const inp=$('#depart'); if(inp) inp.value=t.toISOString().slice(0,16);
  });
  $('#now')?.click();

  $('#locate')?.addEventListener('click', locateAndSetOrigin);
  $('#pickB')?.addEventListener('click', ()=>{
    selectingB = !selectingB;
    $('#pickB').classList.toggle('active', selectingB);
    showStatus(selectingB ? 'Bật: Click lên bản đồ để đặt B' : 'Tắt chọn B bằng click');
  });
  $('#nearest')?.addEventListener('click', suggestNearestBranch);
  $('#draw')?.addEventListener('click', drawRoute);
  $('#clear')?.addEventListener('click', ()=>{
    clearRouteLayer(); updateETA(null); setTrafficText(null); setWeatherText(null); setNotes(''); showStatus('Đã xoá lộ trình.');
  });
  $('#reset')?.addEventListener('click', ()=>{
    clearAll(); updateETA(null); setTrafficText(null); setWeatherText(null); setNotes(''); showStatus('Đã chọn lại chuyến đi.');
  });

  $('#goB')?.addEventListener('click', onGoB);

  setupSearchB(); updateGoBState();
  fitAll();
}

/* ========= CHI NHÁNH ========= */
function renderBranches(items){
  branchMarkers.forEach(m=>m.remove()); branchMarkers=[];
  const list=$('#branch-list'); if(list) list.innerHTML='';
  items.forEach(b=>{
    const m=L.marker([b.lat,b.lng]).addTo(map); branchMarkers.push(m);
    const go=document.createElement('button'); go.textContent='Chỉ đường tới đây'; go.className='btn primary';
    go.onclick=()=>routeTo([b.lat,b.lng],b.name);
    const div=document.createElement('div'); div.innerHTML=`<b>${b.name}</b><br/><span class="addr">${b.address}</span><br/>`; div.appendChild(go);
    m.bindPopup(div);

    if(list){
      const card=document.createElement('div'); card.className='card';
      card.innerHTML=`<div class="name">${b.name}</div><div class="addr">${b.address}</div><div class="actions"><button class="btn" data-act="focus">Xem trên bản đồ</button><button class="btn primary" data-act="route">Chỉ đường tới đây</button></div>`;
      list.appendChild(card);
      card.querySelector('[data-act="focus"]').onclick=()=>{map.setView([b.lat,b.lng],15); m.openPopup();};
      card.querySelector('[data-act="route"]').onclick=()=>routeTo([b.lat,b.lng],b.name);
    }
  });
  updateBranchHeaderCount(items.length);
}

/* ========= VỊ TRÍ HIỆN TẠI ========= */
async function locateAndSetOrigin(){
  if(!navigator.geolocation){ showStatus('Thiết bị không hỗ trợ định vị.'); return; }
  const pos=await new Promise((res,rej)=>navigator.geolocation.getCurrentPosition(res,rej,{enableHighAccuracy:true,timeout:8000})).catch(()=>null);
  if(!pos){ showStatus('Không lấy được vị trí'); return; }
  const {latitude:lat,longitude:lng}=pos.coords;
  if(!userMarker){
    userMarker=L.marker([lat,lng],{icon:L.divIcon({className:'pulse-dot',iconSize:[14,14]})}).addTo(map).bindPopup('Vị trí hiện tại');
  }else userMarker.setLatLng([lat,lng]);
  map.setView([lat,lng],14);
  clearRouteLayer();
  showStatus('Đã cập nhật vị trí hiện tại.');
  updateGoBState();
}

/* ========= B (điểm đến) ========= */
function setB([lat,lng], place=''){
  nameB = place || nameB;
  const popupHTML = `Điểm B: ${nameB||''} <br><button id="popup-go" style="margin-top:6px;padding:6px 10px;border-radius:8px;border:1px solid #0b57d0;background:#0b57d0;color:#fff;cursor:pointer">Chỉ đường tới đây</button>`;
  if(!markerB){
    markerB=L.marker([lat,lng],{icon:RedIcon,draggable:true}).addTo(map).bindPopup(popupHTML)
      .on('popupopen', ()=>{ const btn=document.getElementById('popup-go'); if(btn) btn.onclick=async ()=>{ if(!userMarker) await locateAndSetOrigin(); if(userMarker) await drawRoute(); }; });
    markerB.on('dragend', ()=>{ updateGoBState(); if(userMarker) drawRoute(); });
  }else{
    markerB.setLatLng([lat,lng]).bindPopup(popupHTML);
  }
  map.setView([lat,lng],14);
  updateGoBState();
}

/* ========= ROUTING & AI ========= */
function clearRouteLayer(){ if(routeLayer){ map.removeLayer(routeLayer); routeLayer=null; } }

async function osrmRoute(origin, dest){
  const url=`${OSRM_BASE}/cycling/${origin[1]},${origin[0]};${dest[1]},${dest[0]}?overview=full&geometries=geojson&annotations=distance,duration`;
  const r=await fetch(url); if(!r.ok) throw new Error('OSRM không phản hồi');
  const data=await r.json(); if(!data.routes||!data.routes[0]) throw new Error('Không lấy được tuyến OSRM');
  return data.routes[0];
}
function sampleAlong(coords, n=60){
  if(coords.length<=n) return coords;
  const out=[]; const step=(coords.length-1)/(n-1);
  for(let i=0;i<n;i++) out.push(coords[Math.round(i*step)]);
  return out;
}
function segmentPolylineColored(coords, levels){
  const layers=[];
  for(let i=0;i<coords.length-1;i++){
    const col = levels[i]==='low'?COLORS.low : (levels[i]==='med'?COLORS.med:COLORS.high);
    layers.push(L.polyline([coords[i], coords[i+1]], {weight:7, color:col, opacity:1}));
  }
  return L.featureGroup(layers);
}

async function drawRoute(){
  if(!userMarker){ showStatus('Chưa có điểm xuất phát. Bấm "Vị trí hiện tại".'); return; }
  if(!markerB){ showStatus('Chưa có điểm đến. Hãy chọn/nhập B.'); return; }
  showStatus('Đang tính tuyến (OSRM + AI)…', 3000);

  try{
    const departInput = $('#depart')?.value;
    const depart = departInput ? new Date(departInput) : new Date();

    const origin=[userMarker.getLatLng().lat, userMarker.getLatLng().lng];
    const dest=[markerB.getLatLng().lat, markerB.getLatLng().lng];

    // 1) OSRM
    const route = await osrmRoute(origin, dest);
    const coords = route.geometry.coordinates.map(([lng,lat])=>[lat,lng]);
    const durationSec = route.duration;
    updateETA(durationSec/60);

    // 2) AI KẸT XE – tô màu đoạn
    const base = trafficBaseScore(depart);
    const sampled = sampleAlong(coords, 60);
    const levels=[];
    for(const [lat,lng] of sampled){
      const s = clamp(0.2 + 0.8*base*nearestCityFactor(lat,lng));
      levels.push(trafficLevel(s));
    }
    clearRouteLayer();
    routeLayer = segmentPolylineColored(sampled, levels).addTo(map);
    map.fitBounds(routeLayer.getBounds(), {padding:[30,30]});

    // Đa số
    const cnt = {low:0,med:0,high:0}; levels.forEach(l=>cnt[l]++);
    const top = Object.entries(cnt).sort((a,b)=>b[1]-a[1])[0][0];
    setTrafficText(top==='low'?'Không kẹt': (top==='med'?'Hơi kẹt':'Kẹt'));

    // Khung thời gian kẹt sắp tới → kết thúc
    const repIdx = Math.floor(sampled.length*0.5);
    const repLatLng = sampled[repIdx] || sampled[0];
    const win = predictTrafficWindow(repLatLng, depart, durationSec);

    // 3) THỜI TIẾT dọc tuyến + tại thời điểm ĐẾN
    const distCum = [0];
    for(let i=1;i<coords.length;i++){
      distCum[i] = distCum[i-1] + distKm(coords[i-1][0],coords[i-1][1],coords[i][0],coords[i][1]);
    }
    const totalKm = distCum[distCum.length-1] || 0.0001;
    const whenAt = idx => new Date(depart.getTime() + durationSec*1000*(distCum[idx]/totalKm));
    const idxs = [0.1,0.35,0.65,0.9].map(f=>Math.floor(f*(coords.length-1)));
    const checkpoints = [...new Set(idxs)].map(i=>coords[i]);

    const weatherInfos = [];
    for(let k=0;k<checkpoints.length;k++){
      const i = idxs[k];
      const p = checkpoints[k];
      const dt = whenAt(i);
      const json = await fetchWeather(p[0], p[1]);
      const times = json.hourly.time; const ix = pickHourIndex(times, dt);
      const pp   = json.hourly.precipitation_probability[ix];
      const wind = json.hourly.windspeed_10m[ix];
      const cloud= json.hourly.cloudcover[ix];
      const risk = weatherRisk(pp, wind);
      const lvl = riskLevel(risk);
      weatherInfos.push({lat:p[0], lng:p[1], dt, pp, wind, cloud, risk, lvl});
    }

    weatherInfos.forEach(w=>{
      const col = w.lvl==='low'?COLORS.low: (w.lvl==='med'?COLORS.med:COLORS.high);
      L.circleMarker([w.lat,w.lng],{radius:6,color:'#000',weight:1,fill:true,fillOpacity:0.9,fillColor:col})
        .addTo(routeLayer)
        .bindPopup(`<b>Thời tiết dự kiến</b><br>
          Lúc: ${w.dt.toLocaleString()}<br>
          Mưa: ${w.pp}% • Gió: ${w.wind} km/h • Mây: ${w.cloud}%<br>
          Rủi ro: <b style="color:${col}">${w.lvl.toUpperCase()}</b>`);
    });

    const arriveAt = new Date(depart.getTime() + durationSec*1000);
    const wDest = await fetchWeather(dest[0], dest[1]);
    const ixA = pickHourIndex(wDest.hourly.time, arriveAt);
    const ppA = wDest.hourly.precipitation_probability[ixA];
    const rainA = (ppA >= 50) || (wDest.hourly.precipitation[ixA] > 0.1);
    const sunnyA = (ppA <= 20) && (wDest.hourly.cloudcover[ixA] <= 30);

    let weatherNoteHTML = '';
    if(sunnyA){
      setWeatherText(`Nắng (đến ${hhmm(arriveAt)})`);
      weatherNoteHTML += `<div class="note w-sun">☀️ <b>Nắng</b> lúc ${hhmm(arriveAt)} – Phù hợp để bắt đầu chuyến đi.</div>`;
    }else if(rainA){
      setWeatherText(`Có mưa (đến ${hhmm(arriveAt)})`);
      weatherNoteHTML += `<div class="note w-rain">🌧️ <b>Dự báo mưa</b> nếu đi ngay (đến khoảng ${hhmm(arriveAt)}).</div>`;
    }else{
      setWeatherText(`Ổn định (đến ${hhmm(arriveAt)})`);
    }

    let trafficNoteHTML = '';
    if(win){
      if(win.lv==='high'){
        setTrafficText(`Kẹt (đỏ) ${hhmm(win.t0)}–${hhmm(win.t1)}`);
        trafficNoteHTML = `<div class="note t-red">🚦 <b>Đường đỏ</b>: <b>kẹt xe</b> ${hhmm(win.t0)}–${hhmm(win.t1)}.</div>`;
      }else if(win.lv==='med'){
        setTrafficText(`Hơi kẹt (vàng) ${hhmm(win.t0)}–${hhmm(win.t1)}`);
        trafficNoteHTML = `<div class="note t-yellow">🚦 <b>Đường vàng</b>: <b>hơi kẹt</b> ${hhmm(win.t0)}–${hhmm(win.t1)}.</div>`;
      }else{
        setTrafficText('Không kẹt (xanh)');
        trafficNoteHTML = `<div class="note t-green">🚦 <b>Đường xanh</b>: <b>không kẹt</b> trong khung giờ dự kiến.</div>`;
      }
    }

    setNotes(trafficNoteHTML + weatherNoteHTML);
    showStatus('Đã vẽ tuyến + ghi chú kẹt xe & thời tiết.');
  }catch(e){
    console.error(e);
    showStatus('Lỗi vẽ tuyến/AI: '+e.message, 4000);
  }
}

/* ========= ROUTE TO B nhanh ========= */
async function routeTo([lat,lng], name=''){
  setB([lat,lng], name);
  if(!userMarker) await locateAndSetOrigin();
  if(userMarker) await drawRoute();
}

/* ========= GỢI Ý TRẠM GẦN NHẤT ========= */
function nearestBranch(lat,lng){
  let best=null, bestD=Infinity;
  for(const b of BRANCHES){
    const d=distKm(lat,lng,b.lat,b.lng);
    if(d<bestD){bestD=d; best={...b,distance:d};}
  } return best;
}
async function suggestNearestBranch(){
  if(!userMarker) await locateAndSetOrigin();
  if(!userMarker) return;
  const {lat,lng}=userMarker.getLatLng();
  const b=nearestBranch(lat,lng);
  showStatus(`Gần nhất: ${b.name} (~${b.distance.toFixed(1)} km)`, 3000);
  await routeTo([b.lat,b.lng],b.name);
}

/* ========= GỢI Ý B – 3 tầng ========= */
function sortByPivot(items, pivotLat, pivotLng){
  return items.sort((p,q)=>{
    const dp=distKm(p.lat,p.lng,pivotLat,pivotLng);
    const dq=distKm(q.lat,q.lng,pivotLat,pivotLng);
    return dp-dq;
  });
}
function setupSearchB(){
  const input=$('#searchB'); const box=$('#sugB'); if(!input||!box) return;
  let timer=null;

  input.addEventListener('input', ()=>{
    const q=input.value.trim(); clearTimeout(timer); updateGoBState();
    if(q.length<3){ box.style.display='none'; box.innerHTML=''; return; }
    timer=setTimeout(async ()=>{
      showLoading(box);
      const pivotLngLat = userMarker ? [userMarker.getLatLng().lng, userMarker.getLatLng().lat] : HCMC_PIVOT;
      const pivotLatLng = userMarker ? [userMarker.getLatLng().lat, userMarker.getLatLng().lng] : [HCMC_CENTER[0], HCMC_CENTER[1]];
      const items = await smartGeocode(q, pivotLngLat);
      const sorted = sortByPivot(items, pivotLatLng[0], pivotLatLng[1]).slice(0,10);

      box.innerHTML = sorted.map(it =>
        `<div class="item" data-lat="${it.lat}" data-lng="${it.lng}">
           ${it.title}<small>${it.subtitle||''}</small>
         </div>`).join('');
      box.style.display = sorted.length ? 'block' : 'none';

      box.querySelectorAll('.item').forEach(el=>{
        el.onclick = ()=>{
          const lat=parseFloat(el.getAttribute('data-lat'));
          const lng=parseFloat(el.getAttribute('data-lng'));
          const name=el.textContent.trim();
          setB([lat,lng], name);
          input.value = name;
          box.style.display='none';
          updateGoBState();
        };
      });
    }, 280);
  });

  input.addEventListener('keydown', async (e)=>{
    if(e.key==='Enter'){
      e.preventDefault();
      const itemsEls = Array.from(document.querySelectorAll('#sugB .item'));
      if(itemsEls.length){ itemsEls[0].click(); }
      else{
        const q = input.value.trim();
        if(q.length>=3){
          const pivot = userMarker ? [userMarker.getLatLng().lng, userMarker.getLatLng().lat] : HCMC_PIVOT;
          const results = await smartGeocode(q, pivot);
          if(results.length){
            const top = results[0];
            setB([top.lat, top.lng], `${top.title} ${top.subtitle?'- '+top.subtitle:''}`);
            updateGoBState();
          }else{ showStatus('Không thấy kết quả ở Việt Nam cho từ khoá này.'); }
        }
      }
      $('#goB')?.focus();
    }
  });

  input.addEventListener('blur', ()=> setTimeout(()=> box.style.display='none', 200));
}
function showLoading(box){ box.innerHTML = `<div class="item"><small>Đang tìm…</small></div>`; box.style.display='block'; }
async function smartGeocode(q, proximity=[HCMC_PIVOT[0], HCMC_PIVOT[1]]){
  try{ if(MAPBOX_TOKEN){ const m = await geocodeMapbox(q, proximity); if(m && m.length) return m; } }catch{}
  try{ const p = await geocodePhoton(q, proximity); if(p && p.length) return p; }catch{}
  try{ const n = await geocodeNominatim(q); if(n && n.length) return n; }catch{}
  return [];
}
async function geocodeMapbox(q, proximity=[HCMC_PIVOT[0], HCMC_PIVOT[1]]){
  const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(q)}.json` +
    `?access_token=${MAPBOX_TOKEN}&autocomplete=true&language=vi&country=VN&limit=10` +
    `&types=poi,address,place,locality,neighborhood,street` +
    `&proximity=${proximity[0]},${proximity[1]}` +
    `&bbox=${VN_VIEWBOX.join(',')}`;
  const r = await fetch(url); const data = await r.json();
  return (data.features||[]).map(f=>({ lat:f.center[1], lng:f.center[0], title:f.text_vi||f.text||f.place_name, subtitle:f.place_name }));
}
async function geocodePhoton(q, proximity=[HCMC_PIVOT[0], HCMC_PIVOT[1]]){
  const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(q)}&lang=vi&limit=10` +
              `&lat=${proximity[1]}&lon=${proximity[0]}` +
              `&bbox=${VN_VIEWBOX[0]},${VN_VIEWBOX[1]},${VN_VIEWBOX[2]},${VN_VIEWBOX[3]}`;
  const r = await fetch(url); const data = await r.json();
  return (data.features||[]).map(f=>{
    const p=f.properties||{};
    const title = p.name || p.street || p.city || 'Địa điểm';
    const subtitle = [p.housenumber && p.street ? `${p.housenumber} ${p.street}`:null, p.street && !p.housenumber ? p.street:null, p.city, p.state, p.country].filter(Boolean).join(', ');
    return {lat:f.geometry.coordinates[1], lng:f.geometry.coordinates[0], title, subtitle};
  });
}
async function geocodeNominatim(q){
  const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&addressdetails=1&accept-language=vi&limit=10&countrycodes=vn&viewbox=${VN_VIEWBOX[0]},${VN_VIEWBOX[3]},${VN_VIEWBOX[2]},${VN_VIEWBOX[1]}&bounded=1`;
  const r = await fetch(url, {headers:{'User-Agent':'bike-rental-student-project'}}); const data = await r.json();
  return (data||[]).map(x=>({ lat: parseFloat(x.lat), lng: parseFloat(x.lon), title: x.display_name.split(',')[0], subtitle: x.display_name }));
}

/* ========= CLEAR/ALL/FIT ========= */
function clearAll(){
  clearRouteLayer();
  if(markerB){map.removeLayer(markerB); markerB=null; nameB='';}
  if(userMarker){map.removeLayer(userMarker); userMarker=null;}
  updateGoBState();
}
function fitAll(){
  const g=new L.LatLngBounds();
  branchMarkers.forEach(m=>g.extend(m.getLatLng()));
  if(userMarker) g.extend(userMarker.getLatLng());
  if(markerB) g.extend(markerB.getLatLng());
  if(routeLayer) g.extend(routeLayer.getBounds());
  if(g.isValid()) map.fitBounds(g,{padding:[30,30]});
}

/* ========= GO BUTTON ========= */
async function onGoB(){
  if(markerB){
    if(!userMarker) await locateAndSetOrigin();
    if(userMarker) await drawRoute();
    return;
  }
  const q = $('#searchB')?.value.trim() || '';
  if(q.length < 3){ showStatus('Nhập ít nhất 3 ký tự để tìm B.'); return; }
  const pivot = userMarker ? [userMarker.getLatLng().lng, userMarker.getLatLng().lat] : HCMC_PIVOT;
  const results = await smartGeocode(q, pivot);
  if(!results.length){ showStatus('Không thấy kết quả ở Việt Nam cho từ khoá này.'); return; }
  const top = results[0];
  setB([top.lat, top.lng], `${top.title} ${top.subtitle?'- '+top.subtitle:''}`);
  if(!userMarker) await locateAndSetOrigin();
  if(userMarker) await drawRoute();
}

/* ========= START ========= */
document.addEventListener('DOMContentLoaded', init);
