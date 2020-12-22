<template>
    <jet-dialog-modal :show="booking_request" @close="closeModal">
        <template #title>
            Update Booking Request
        </template>

        <template #content>
            <div class="m-6">
                <jet-label for="name" value="Room" />
                <!-- <jet-input
                    id="room_id"
                    type="room_id"
                    class="mt-1 block w-full"
                    v-model="form.room_id"
                    autofocus
                /> -->

                <select v-model="form.room_id" class="mt-1 block w-full" name="rooms" id="room_id">
                    <option :value="form.room_id" selected="selected"> {{form.roomName}}</option>
                    <option v-for="room in availableExcludingCurrent" :key="room.id" :value="room.id">{{room.name}}</option>
                </select>
                <jet-input-error :message="form.error('room_id')" class="mt-2" />
            </div>

            <div class="m-6">
                <jet-label for="start_time" value="Start Time" />
                <jet-input
                    id="start_time"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.start_time"
                    autofocus
                />
                <jet-input-error :message="form.error('start_time')" class="mt-2" />
            </div>

            <div class="m-6">
                <jet-label for="end_time" value="End Time" />
                <jet-input
                    id="end_time"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.end_time"
                    autofocus
                />
                <jet-input-error
                    :message="form.error('end_time')"
                    class="mt-2"
                />
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="closeModal">
                Nevermind
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                @click.native="updateBookingRequest"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Update
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetActionMessage from "@src/Jetstream/ActionMessage";
import JetFormSection from "@src/Jetstream/FormSection";
import JetDialogModal from "@src/Jetstream/DialogModal";
import JetButton from "@src/Jetstream/Button";
import JetSecondaryButton from "@src/Jetstream/SecondaryButton";
import JetLabel from "@src/Jetstream/Label";
import JetInput from "@src/Jetstream/Input";
import JetInputError from "@src/Jetstream/InputError";

export default {
    components: {
        JetActionMessage,
        JetFormSection,
        JetDialogModal,
        JetButton,
        JetSecondaryButton,
        JetLabel,
        JetInput,
        JetInputError
    },

    props: {
        booking_request: {
            type: Object,
            required: false
        },
        availableRooms: {
            type: Array,
            default: function() {
                return [];
        }
        }
    },

    data() {
        return {
            availableExcludingCurrent: [],
            form: this.$inertia.form(
                {
                    user_id: null,
                    room_id: null,
                    start_time: null,
                    end_time: null,
                    roomName: null
                },
                {
                    bag: "updateBookingRequest"
                }
            )
        };
    },

    methods: {
        closeModal() {
            if (this.$page && this.$page.errorBags.updateBookingRequest) {
                delete this.$page.errorBags.updateBookingRequest;
            }
            this.$emit("close");
        },
        updateBookingRequest() {
            this.form
                .put("/bookings/" + this.booking_request?.id, {
                    preserveState: true
                })
                .then(() => {
                    if (this.form.successful) {
                        this.closeModal();
                    }
                });
        }
    },
    watch: {
        booking_request(booking_request) {
            this.form.user_id = booking_request?.user_id;
            this.form.room_id = booking_request?.room_id;
            this.form.start_time = booking_request?.start_time.substring(0, 16);
            this.form.end_time = booking_request?.end_time.substring(0, 16);
            this.form.roomName = booking_request?.room.name;

            this.availableExcludingCurrent = this.availableRooms.filter(function( room ) {
                return room.id !== booking_request?.room_id;
            });
        }
    }
};
</script>