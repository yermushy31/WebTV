/*

class Widgets {
    #ApiKey;
    #info;

    constructor(ApiKey, info) {
        this.#ApiKey = ApiKey;
        this.#info = info;
    }
    async weather() {
        const data = await fetch("https://api.openweathermap.org/data/2.5/weather?id=" + this.#info.cityid + "&lang=fr&units=metric&appid=" + this.#ApiKey.weatherApi);
        const info = await data.json();
        return info;
    }
    
    async news() {
        const data = await fetch("https://newsapi.org/v2/top-headlines?q=" + this.#info.keyword + "&country=" + this.#info.language + "&language=" + this.#info.language + "&pageSize=20&apiKey=" + this.#ApiKey.newsApi);
        const info = await data.json();
        return info;
    }
}


async function main() {
    const keys = {
        weatherApi: "947c00c93252788bf42802507b3aab97",
        newsApi: "6f70fc1752ef4fd6b2ad0e3589a84236"

    }
    const infos = {
        cityid: "2972315",
        keyword: "France",
        language: "fr"

    }
    function getRandom(min, max) {
        return Math.random() * (max - min) + min;
    }
    const widget = new Widgets(keys, infos);

    const weather = await widget.weather();
    const newsData = await widget.news();
    

    var customBoxes = document.querySelectorAll(".temperature");
    var weatherIcons = document.querySelectorAll(".icon");
    var cityName = document.querySelectorAll(".city");

    var newsHTML = "";
    var MAX = Object.keys(parsedJson.articles).length;
    let randomindex = Math.round(getRandom(0, MAX));
    if (parsedJson.articles[randomindex].title != undefined)
        newsHTML += '<div class="messagedefilant"><div data-text="' + parsedJson.articles[randomindex].title + '!---"><span>' + parsedJson.articles[randomindex].title + '</span></div></div>'
    var second_area = document.querySelectorAll(".news");
    second_area.forEach(item => item.innerHTML = newsHTML);
    "<div data-text='" + parsedJson.articles[randomindex].title + "'><span>"+ parsedJson.articles[randomindex].title + "</span></div>";


    cityName.forEach(item => {
        item.innerHTML = weather.name;
    });
    customBoxes.forEach((item) => {
        item.innerHTML = Math.round(weather.main.temp) + "&deg;";

    });

    var img_source = "";
    weatherIcons.forEach((item, index) => {
        weatherIcons[index].src = "https://openweathermap.org/img/w/" + weather.weather[0].icon + ".png";
    });
}
*/
