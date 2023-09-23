<template>
  <div class="login-container">
    <div class="login-right-tools">
      <vab-language />
      <vab-color-picker />
      <vab-dark />
    </div>
    <div class="login-form">
      <img alt="" class="left-img" :src="leftImg" />
      <el-form ref="formRef" label-position="left" :model="form" :rules="rules" @submit.prevent>
        <div class="title">hello !</div>
        <div class="title-tips">{{ translate('欢迎来到') }}{{ title }}！</div>

        <!--    USER NAME      -->
        <el-form-item prop="username">
          <el-input v-model.trim="form.username" clearable :placeholder="translate('User name')" type="text">
            <template #prefix>
              <vab-icon icon="user-line" />
            </template>
          </el-input>
        </el-form-item>
        <!--    USER NAME //    -->

        <!--    PASSWORD      -->
        <el-form-item prop="password">
          <el-input
            :key="passwordType"
            ref="passwordRef"
            v-model.trim="form.password"
            clearable
            :placeholder="translate('Password')"
            :type="passwordType"
            @keyup.enter="handleLogin"
          >
            <template #prefix>
              <vab-icon icon="lock-line" />
            </template>
          </el-input>
        </el-form-item>
        <!--    PASSWORD  //  -->

        <!--    DEVICE NAME      -->
        <el-form-item prop="device_name">
          <el-input v-model.trim="form.device_name" clearable :placeholder="translate('Device name')" type="text">
            <template #prefix>
              <vab-icon icon="device-fill" />
            </template>
          </el-input>
        </el-form-item>

        <!--    DEVICE NAME  //  -->

        <!-- 验证码验证逻辑需自行开发，如不需要验证码功能建议注释 -->
        <el-button class="login-btn" :loading="loading" native-type="submit" type="primary" @click.prevent="handleLogin">
          {{ translate('登录') }}
        </el-button>
      </el-form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { translate } from '/@/i18n'
import { useSettingsStore } from '/@/store/modules/settings'
import { useUserStore } from '/@/store/modules/user'
import { getImageUrl } from '/@/utils/imageUrl'
import { isPassword } from '/@/utils/validate'

defineOptions({
  name: 'Login',
})

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const settingsStore = useSettingsStore()
const { title } = storeToRefs(settingsStore)
const leftImg = ref<string>(getImageUrl(`assets/login_images/left_img.png`))
const login = (form: any) => userStore.login(form)
const loading = ref<boolean>(false)
const passwordType = ref<string>('password')
const redirect = ref<any>(undefined)
const formRef = ref<any>(null)
const passwordRef = ref<any>(null)
const form = reactive<any>({
  username: '',
  password: '',
  device_name: '',
})

const validateUsername = (rule: any, value: any, callback: any) => {
  if ('' === value) callback(new Error(translate('用户名不能为空')))
  else callback()
}
const validateDeviceName = (rule: any, value: any, callback: any) => {
  if ('' === value) callback(new Error(translate('Device Name field is required')))
  else callback()
}
const validatePassword = (rule: any, value: any, callback: any) => {
  if (!isPassword(value)) callback(new Error(translate('密码不能少于6位')))
  else callback()
}

const rules = reactive<any>({
  username: [
    {
      required: true,
      trigger: 'blur',
      validator: validateUsername,
    },
  ],
  password: [
    {
      required: true,
      trigger: 'blur',
      validator: validatePassword,
    },
  ],
  device_name: [
    {
      required: true,
      trigger: 'blur',
      validator: validateDeviceName,
    },
  ],
})

const handleRoute = () => {
  return redirect.value === '/404' || redirect.value === '/403' ? '/' : redirect.value
}

const handleLogin = async () => {
  if (formRef.value)
    formRef.value.validate(async (valid: any) => {
      if (valid)
        try {
          loading.value = true
          await login(form).catch(() => {
            loading.value = false
          })
          await router.push(handleRoute())
        } finally {
          loading.value = false
        }
    })
}

watchEffect(() => {
  redirect.value = (route.query && route.query.redirect) || '/'
})
</script>

<style lang="scss" scoped>
@import 'library/styles/view/login';
</style>
