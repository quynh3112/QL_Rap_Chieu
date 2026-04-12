document.addEventListener("DOMContentLoaded", () => {
   const cities = document.getElementById('city');
   const branches = document.getElementById('branches');
   const films=document.getElementById('items')

   async function loadCities() {
      try {
         const res = await fetch('../../Controllers/cityController.php');
         const data = await res.json();

         data.forEach(c => {
            const city = document.createElement('p');
            city.textContent = c.thanhPho;
<<<<<<< HEAD
            city.className="tp"
=======
>>>>>>> dev-food

            city.style.cursor = "pointer";

            city.onclick = () => {
<<<<<<< HEAD
               document.querySelectorAll('.tp').forEach(i => {
                  i.classList.remove('active')
               })
               city.classList.add('active')

=======
>>>>>>> dev-food
               loadBranches(c.thanhPho);
            };

            cities.appendChild(city);
         });

      } catch (err) {
         console.log(err);
      }
   }

   async function loadBranches(thanhpho) {
      try {
<<<<<<< HEAD
         branches.innerHTML = "<h1>Rạp</h1>"; 
=======
         branches.innerHTML = ""; 
>>>>>>> dev-food

         const res = await fetch(
            `../../Controllers/branchController.php?thanhPho=${encodeURIComponent(thanhpho)}`
         );

         const data = await res.json();

         data.forEach(b => {
            const branch = document.createElement('p');
            branch.textContent = b.tenBranch;
<<<<<<< HEAD
            branch.className="chiNhanh"

            branch.style.cursor = "pointer";
            branch.onclick=()=>{
               document.querySelectorAll('.chiNhanh').forEach(i=>{
                  i.classList.remove('active')
               })
               branch.classList.add('active')
=======

            branch.style.cursor = "pointer";
            branch.onclick=()=>{
>>>>>>> dev-food
                renderFilm(b.branchId)
            }

          

            branches.appendChild(branch);
         });

      } catch (err) {
         console.log(err);
      }
   }
   async function  renderFilm(branchId) {
    try{
        const res=await fetch(`../../Controllers/branchController.php?branchId=${encodeURIComponent(branchId)}`)
        const data=await res.json()
<<<<<<< HEAD
        films.innerHTML="";
        if (!Array.isArray(data)) {
              alert(data.message)
                return;
            }
=======
        films.innerHTML='';
>>>>>>> dev-food
        data.forEach(f=>{
            const item=document.createElement('div')
            item.className='item'
            const title=document.createElement('h3')
            const img=document.createElement('img')
            const duration=document.createElement('h5')
            title.textContent=f.tenPhim
            img.src=f.img
            duration.textContent=`Thời Lượng: ${f.thoiLuong}`
            item.append(img,title,duration)
            films.appendChild(item)
        })


    }
    catch(err){
        console.log(err)
    }
    
   }

   loadCities();
});