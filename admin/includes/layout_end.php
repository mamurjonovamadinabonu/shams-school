    </div><!-- .content -->
  </div><!-- .main -->
</div><!-- .admin-wrap -->

<script>
// Clock
function updateClock(){
  const now=new Date();
  const t=now.toLocaleTimeString('uz-UZ',{hour:'2-digit',minute:'2-digit'});
  const d=now.toLocaleDateString('uz-UZ',{day:'2-digit',month:'short',year:'numeric'});
  const el=document.getElementById('clock');
  if(el)el.textContent=d+' '+t;
}
updateClock();setInterval(updateClock,1000);

// Sidebar toggle
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
}

// Auto-close flash
document.querySelectorAll('.flash').forEach(el=>{
  setTimeout(()=>el.style.opacity='0',4000);
  setTimeout(()=>el.remove(),4500);
});

// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(el=>{
  el.addEventListener('click',e=>{
    if(!confirm(el.dataset.confirm)){e.preventDefault()}
  });
});
</script>
</body>
</html>
