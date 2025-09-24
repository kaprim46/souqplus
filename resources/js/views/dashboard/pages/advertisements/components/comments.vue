<template>
    <div class="flex flex-col gap-1 mb-5" v-if="!wishComment">
        <label for="status" class="text-gray-800 text-[14.4px] font-semibold">{{ $t('Filter by status') }}</label>
        <v-select
            :value="filters.status"
            id="status"
            @update:value="filters.status = $event"
        >
            <option :value="status.value" v-for="status in [{ label: $t('All'), value: 'all' }, { label: $t('Approved'), value: 'approved' }, { label: $t('Pending'), value: 'pending' }, { label: $t('Rejected'), value: 'rejected' }]" :key="status.value">
                {{ status.label }}
            </option>
        </v-select>
    </div>

    <div v-if="!isLoadingList" class="flex flex-col divide-y divide-gray-200">
        <div class="flex gap-2 py-3" v-for="comment in comments" :key="comment.id">
            <template v-if="wishComment !== comment.id">
                <div class="h-12 w-12 rounded-full relative overflow-hidden">
                    <img class="object-cover absolute inset-0 h-full w-full" :src="comment.user?.avatar_url?? `https://ui-avatars.com/api/?name=${comment.name}&color=333333&background=F4F4F4F4`" :alt="comment.user?.name?? comment.name" />
                </div>
                <div class="flex flex-col gap-1 w-full">
                    <div class="flex justify-between">
                        <div class="font-bold text-gray-800 w-full flex-1">
                            {{ comment.user?.id ? comment.user.name : comment.name }}
                            <i class="icon text-[12px] mgc_check_2_fill text-blue-700"></i>
                        </div>

                        <div class="flex gap-1 items-center">
                            <span class="flex items-center gap-1">
                                <v-badge :color="switchColor(comment.status)" :text="$t(comment.status)" v-if="wishComment !== comment.id"></v-badge>
                                <i class="icon mgc_pen_2_fill text-gray-800 hover:text-blue-800 cursor-pointer"
                                   v-if="wishComment !== comment.id"
                                   @click="wishComment = comment.id"></i>
                                <i class="icon mgc_delete_2_line text-gray-800 hover:text-red-800 cursor-pointer"
                                   v-if="wishComment !== comment.id"
                                   @click="deleteComment(comment.id)"></i>
                            </span>
                        </div>
                    </div>
                    <span class="text-[11.1px] text-gray-600">
                        {{ $t('Posted on :date', { date: comment.created_at }) }}
                    </span>
                    <div class="text-gray-900 font-medium">
                        {{ comment.content }}
                    </div>
                </div>
            </template>
            <div class="flex flex-col gap-5 w-full" v-else>

                <div class="flex flex-col gap-1">
                    <label for="status" class="text-gray-800 text-[14.4px] font-semibold">{{ $t('Status') }}</label>
                    <v-select
                        id="status"
                        :value="comment.status"
                        @update:value="comment.status = $event"
                    >
                        <option value="approved">{{ $t('Approved') }}</option>
                        <option value="pending">{{ $t('Pending') }}</option>
                        <option value="rejected">{{ $t('Rejected') }}</option>
                    </v-select>
                </div>

                <div v-if="comment.status === 'rejected'" class="flex flex-col gap-1">
                    <label for="rejection_reason" class="text-gray-800 text-[14.4px] font-semibold">{{ $t('Rejection reason') }}</label>
                    <v-textarea
                        id="rejection_reason"
                        :value="comment.rejection_reason"
                        @update:input="comment.rejection_reason = $event"
                    />
                </div>

                <div class="flex gap-2 justify-end">
                    <v-button type="button" color="red" @click="wishComment = null">
                        {{ $t('Cancel') }}
                    </v-button>
                    <v-button type="button" color="blue" @click="updateComment(comment)">
                        {{ $t('Update') }}
                    </v-button>
                </div>
            </div>
        </div>

        <!-- Load more -->
        <div class="flex justify-center" v-if="pagination.last_page > 1 && pagination.current_page < pagination.last_page">
            <v-button type="button" :loading="isLoadingList" color="blue" class="w-full" :disabled="isLoadingList" @click="fetchComments(pagination.current_page + 1)">
                {{ $t('Load more') }}
            </v-button>
        </div>
    </div>
    <div v-else class="flex items-center justify-center p-4">
        <v-loader :size="4" :dark="true" />
    </div>
</template>


<script lang="ts" setup>
import {Advertisement} from "@/types/Advertisements";
import {inject, onMounted, ref, watch} from "vue";
import {notify} from "notiwind";
import {trans, wTrans} from "laravel-vue-i18n";

const props = defineProps<{
    advertisement: Advertisement;
}>();

interface CommentInterFace {
    id: number;
    name: string;
    content: string;
    status: string;
    rejection_reason?: string;
    created_at?: string;
    updated_at?: string;
    user?: {
        id: number;
        name: string;
        avatar_url?: string;
        is_verified: boolean;
    }
}

const comments          = ref<CommentInterFace[]>([]);
const pagination        = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});
const filters           = ref({
    status: 'all',
});
const isLoadingList     = ref<boolean>(false);
const wishComment       = ref<number | null>(null);
const swal              = inject('$swal');

watch(filters, async () => {
    pagination.value = {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
    };
    comments.value = [];
    await fetchComments();
}, { deep: true });

const switchColor = (status: string) => {
    switch (status) {
        case 'approved':
            return 'green';
        case 'pending':
            return 'yellow';
        case 'rejected':
            return 'red';
        default:
            return 'gray';
    }
};
const fetchComments = async (page = 1) => {
    if(!props.advertisement.id) return;

    isLoadingList.value = true;

    let params = {} as {
        status?: string;
    };

    if(filters.value.status !== 'all') {
        params.status = filters.value.status;
    }

    try {
        const { data } = await axios.get(`/advertisements/${props.advertisement.id}/comments?page=${page}`, {
            params,
        });

        if(data.ok) {
            comments.value = [...comments.value, ...data.comments.data]
            pagination.value = {
                current_page: data.comments.current_page,
                last_page: data.comments.last_page,
                per_page: data.comments.per_page,
                total: data.comments.total,
            };
        }
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingList.value = false;
    }
};
const updateComment = async (comment: CommentInterFace) => {
    if(!props.advertisement.id) return;

    isLoadingList.value = true;

    try {
        const { data } = await axios.put(`/dashboard/advertisements/${props.advertisement.id}/comments/${comment.id}`, {
            status: comment.status,
            rejection_reason: comment.rejection_reason,
        });

        if(data.ok) {
            comments.value = comments.value.map((c) => {
                if(c.id === comment.id) {
                    return {
                        ...c,
                        status: comment.status,
                        rejection_reason: comment.rejection_reason,
                    };
                }
                return c;
            });

            wishComment.value = null;
        }


        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000) // 2s
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingList.value = false;
    }
};
const deleteComment = async (id: number) => {
    if(!props.advertisement.id) return;

    const { isConfirmed } = await swal.fire({
        title: trans('Are you sure?'),
        text: trans('You won\'t be able to revert this!'),
        icon: 'warning',
        showCancelButton: true,
        confirm: trans('Yes, delete it!'),
        cancel: trans('No, cancel!'),
    });

    if(!isConfirmed) return;

    try {
        const { data } = await axios.delete(`/dashboard/advertisements/${props.advertisement.id}/comments/${id}`);

        if(data.ok) {
            comments.value = comments.value.filter((c) => c.id !== id);
        }

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000) // 2s
    } catch (e) {
        console.error(e);
    }
};

onMounted(async () => {
    await fetchComments();
});
</script>
