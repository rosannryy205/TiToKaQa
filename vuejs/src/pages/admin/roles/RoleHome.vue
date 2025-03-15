<template>
    ĐÂY LÀ TRANG ROLE
    <router-view></router-view>
</template>
<script>
import { useMenu } from '@/stores/use-menu';
export default {
setup() {
    useMenu().onSelectedKeys(["admin-roles"]);
}
}
</script>