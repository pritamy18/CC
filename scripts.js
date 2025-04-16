const slides = document.querySelectorAll(".slide");
var counter = 0;

slides.forEach((slide, index) => {
    slide.style.left = `${index * 100}%`;
});

const goPrev = () => {
    counter = (counter === 0) ? slides.length - 1 : counter - 1;
    slideImage();
};

const goNext = () => {
    counter = (counter === slides.length - 1) ? 0 : counter + 1;
    slideImage();
  
  };

const slideImage = () => {
    slides.forEach((slide) => {
        slide.style.transform = `translateX(-${counter * 100}%)`;
    });
};
