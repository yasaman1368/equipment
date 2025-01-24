const data = {
  female: {
      adults: {
          cities: ["بندر دیّر", "عسلویه", "جم", "کنگان"],
          categories: ["حفظ کل", "حفظ 20", "حفظ 10", "حفظ 5", "ترتیل", "مفاهیم"]
      },
      teenagers: {
          cities: ["بندر دیّر"],
          categories: ["حفظ نیم جزء", "حفظ 1 جزء", "حفظ 3 جزء", "حفظ 5", "ترتیل"]
      },
      children: {
          cities: ["بندر دیّر"],
          categories: ["حفظ 10 سوره", "حفظ 20 سوره", "حفظ نیم جزء", "حفظ 1 جزء", "فصیح خوانی"]
      }
  },
  male: {
      adults: {
          cities:[
            "بوشهر",
            "تنگستان",
            "دشتستان",
            "دشتی",
            "بندر دیّر",
            "دیلم",
            "کنگان",
            "عسلویه",
            "جم",
            "گناوه"
          ]
          ,
          categories: ["حفظ کل", "حفظ 20", "حفظ 10", "ترتیل", "تحقیق", "مفاهیم"]
      },
      teenagers: {
          cities: ["بندر دیّر"],
          categories: ["قرائت تقلیدی", "حفظ نیم جزء", "حفظ 1 جزء", "حفظ 3 جزء", "اذان"]
      },
      children: {
          cities: ["بندر دیّر"],
          categories: ["حفظ 10 سوره", "حفظ 20 سوره", "حفظ نیم جزء", "حفظ 1 جزء", "فصیح خوانی"]
      }
  }
};
function formUpdate(){
  const gender= document.getElementById('gender').value;
  const ageGroup= document.getElementById('age').value;
  const citiesSelectOption=document.getElementById('city');
  const categoriesSelectOption=document.getElementById('categories');
  
  
  citiesSelectOption.innerHTML='<option value="">شهر را انتخاب کنید...</option>';
  categoriesSelectOption.innerHTML=`<option value="">رشته مسابقه را انتخاب کنید...</option>`
  
  //display  city or cities options
  if (!gender || !ageGroup) {
    citiesSelectOption.disabled=true;
    categoriesSelectOption.disabled=true;
  }
  
  if(gender && ageGroup){
    const selectedData=data[gender][ageGroup];
    
    // Display cities
    selectedData.cities.forEach(city => {
      const option=document.createElement('option');
      option.textContent=city;
      option.value=city;
      citiesSelectOption.appendChild(option);
      citiesSelectOption.disabled=false;

      // Display categories;
      
    });
    
    selectedData.categories.forEach(category=>{
      const option=document.createElement('option');
      option.textContent=category;
      option.value=category;
      categoriesSelectOption.appendChild(option);
      categoriesSelectOption.disabled=false;
    })
}

//set fields
}