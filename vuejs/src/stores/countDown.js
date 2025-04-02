import { ref, computed, onUnmounted } from "vue";

export function useCountdown(initialTime = 60) {
  const countdown = ref(initialTime);
  const isCounting = ref(false);
  let timer = null;

  const formattedTime = computed(() => {
    const minutes = Math.floor(countdown.value / 60);
    const seconds = countdown.value % 60;
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  });

  const startCountdown = () => {
    if (isCounting.value) return;
    isCounting.value = true;
    countdown.value = initialTime;

    timer = setInterval(() => {
      if (countdown.value > 0) {
        countdown.value--;
      } else {
        clearInterval(timer);
        isCounting.value = false;
      }
    }, 1000);
  };

  // Dọn dẹp bộ đếm khi component bị hủy
  onUnmounted(() => {
    if (timer) clearInterval(timer);
  });

  return { formattedTime, isCounting, startCountdown };
}


