<script setup>
import { computed, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import UserAvatar from '@/Components/UserAvatar.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const page = usePage();
const user = computed(() => page.props.auth.user);

const fileInput = ref(null);
const form = useForm({ photo: null });

const roleLabel = computed(
    () =>
        ({
            grower: t('auth.roleGrower'),
            collector: t('auth.roleCollector'),
            buyer: t('photo.roleBuyer'),
        })[user.value.role] ?? user.value.role,
);
const roleClass = computed(() =>
    user.value.role === 'collector' ? 'bg-[#FBEBC8] text-[#8A5A0B]' : 'bg-[#E4EFD6] text-[#2D5715]',
);

const choose = () => fileInput.value.click();

const upload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    form.photo = file;
    form.post(route('profile.photo.update'), {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            form.reset();
            fileInput.value.value = '';
        },
    });
};

const remove = () => router.delete(route('profile.photo.destroy'), { preserveScroll: true });
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
        <div class="h-1" :class="user.role === 'collector' ? 'bg-[#E3B04B]' : 'bg-[#4A8A24]'"></div>

        <div class="flex flex-wrap items-center gap-5 p-5 sm:p-6">
            <UserAvatar :user="user" :size="88" />

            <div class="min-w-0 flex-1">
                <p class="font-display truncate text-xl font-bold text-[#4B2106]">{{ user.name }}</p>
                <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-bold" :class="roleClass">
                    {{ roleLabel }}
                </span>
                <p v-if="user.phone" class="mt-2 text-sm text-[#3A1B0B]/70">
                    {{ t('auth.phone') }}: <span class="font-semibold">{{ user.phone }}</span>
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-full bg-[#2D5715] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#1F3F0F] disabled:opacity-60"
                        :disabled="form.processing"
                        @click="choose"
                    >
                        {{ t('photo.change') }}
                    </button>
                    <button
                        v-if="user.photo_url"
                        type="button"
                        class="rounded-full border border-[#D9CBA9] px-4 py-2 text-sm font-semibold text-[#4B2106] transition hover:bg-[#FDF7E7]"
                        @click="remove"
                    >
                        {{ t('photo.remove') }}
                    </button>
                </div>

                <input ref="fileInput" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="upload" />
                <p class="mt-2 text-xs text-[#3A1B0B]/55">{{ t('photo.hint') }}</p>
                <p v-if="form.errors.photo" class="mt-1 text-sm text-[#A12B1F]">{{ form.errors.photo }}</p>
            </div>
        </div>
    </div>
</template>
