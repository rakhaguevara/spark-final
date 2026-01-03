document.addEventListener("DOMContentLoaded", () => {
    const element = document.getElementById("hero-typing");
    if (!element) return;
  
    const htmlText = `SPARK HERE<br>RESERVE YOUR PARKING<br>EFFORTLESSLY.`;
    const typingSpeed = 45; // ms per karakter
    const startDelay = 300; // delay sebelum mulai (ms)
  
    element.innerHTML = "";
  
    let index = 0;
  
    function typeWriter() {
      if (index < htmlText.length) {
        // handle <br> tag
        if (htmlText[index] === "<") {
          const endTag = htmlText.indexOf(">", index);
          element.innerHTML += htmlText.slice(index, endTag + 1);
          index = endTag + 1;
        } else {
          element.innerHTML += htmlText[index];
          index++;
        }
  
        setTimeout(typeWriter, typingSpeed);
      } else {
        // typing selesai → hapus cursor
        element.classList.add("typing-done");
      }
    }
  
    setTimeout(typeWriter, startDelay);
  });
  