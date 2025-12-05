(function () {
  const marquee = document.getElementById('marquee');
  const track = marquee.querySelector('.marquee-track');
  const originalGroupHTML = marquee.querySelector('.marquee-group').innerHTML;

  function whenImagesLoaded(parent, cb) {
    const imgs = Array.from(parent.querySelectorAll('.marquee-img'));
    if (imgs.length === 0) return cb();
    let remaining = imgs.length;
    imgs.forEach(img => {
      if (img.complete) {
        remaining--;
        if (remaining === 0) cb();
      } else {
        img.addEventListener('load', () => {
          remaining--;
          if (remaining === 0) cb();
        });
        img.addEventListener('error', () => {
          remaining--;
          if (remaining === 0) cb();
        });
      }
    });
  }

  function buildMarquee(speedPxPerSec = 80) {
    track.innerHTML = '';
    const group = document.createElement('div');
    group.className = 'marquee-group';
    group.innerHTML = originalGroupHTML;
    track.appendChild(group);

    whenImagesLoaded(group, () => {
      const marqueeWidth = marquee.clientWidth;
      let safety = 0;
      while (group.scrollWidth < marqueeWidth && safety < 30) {
        const items = Array.from(group.children).map(n => n.cloneNode(true));
        items.forEach(i => group.appendChild(i));
        safety++;
      }
      const groupClone = group.cloneNode(true);
      track.appendChild(groupClone);

      const groupWidth = group.getBoundingClientRect().width;
      const duration = Math.max(1, groupWidth / speedPxPerSec);
      track.style.setProperty('--marquee-duration', duration + 's');
      track.style.willChange = 'transform';
    });
  }

  buildMarquee(80);
  let resizeTimer = null;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => buildMarquee(80), 200);
  });
})();
