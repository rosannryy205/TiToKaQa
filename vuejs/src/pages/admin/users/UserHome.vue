<template>
    ĐÂY LÀ TRANG USER
    <router-view></router-view>
</template>
<script>
import { useMenu } from '@/stores/use-menu';
export default {
setup() {
    useMenu().onSelectedKeys(["admin-users"]);
}
}
</script>