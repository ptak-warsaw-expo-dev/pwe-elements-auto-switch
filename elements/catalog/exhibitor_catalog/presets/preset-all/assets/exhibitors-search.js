document.addEventListener("DOMContentLoaded", () => {
  if (!window.Exhibitors) return;
  const { searchInput, state } = window.Exhibitors;

  if (searchInput && !searchInput.__wired) {
    let t = null;
    searchInput.addEventListener("input", (e)=>{
      clearTimeout(t);
      t = setTimeout(()=>{
        state.q = e.target.value || "";
        window.Exhibitors.reapplyAndReset();
      }, 180);
    });
    searchInput.__wired = true;
  }
});
