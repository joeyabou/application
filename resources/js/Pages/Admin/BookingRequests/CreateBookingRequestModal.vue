<template>
    <jet-dialog-modal :show="room" @close="closeModal" max-width="">
        <template #content>
          
          <div class="overflow-y-auto h-96">
            <h2 class="pb-1">Availabilities</h2>
            <Availabilities :room="room" />
            <div class="pt-5 pb-5">
              <hr/>
            </div>
            <h2>Create a booking request</h2>
            <div class="m-6">
                <jet-label for="name" value="Room" />
                <jet-input
                    id="room_id"
                    type="text"
                    class="mt-1 block w-full"
                    :value="room_name"
                    disabled
                />
                <jet-input-error :message="createBookingRequestForm.error('room_id')" class="mt-2" />
            </div>
            <div v-for="(dates, index) in createBookingRequestForm.reservations" :key="index">
              <jet-input-error :message="createBookingRequestForm.error('reservations.'+index)" class="mt-2" />
              <jet-label  :value="index+1" />
              <div class="m-6">
                  <jet-label for="start_time" value="Start Time" />
                  <jet-input
                      id="start_time"
                      type="datetime-local"
                      class="mt-1 block w-full"
                      v-model="dates.start_time"
                      autofocus
                  />
                  <jet-input-error :message="createBookingRequestForm.error('reservations.'+index+'.start_time')" class="mt-2" />
              </div>

              <div class="m-6">
                  <jet-label for="end_time" value="End Time" />
                  <jet-input
                      id="end_time"
                      type="datetime-local"
                      class="mt-1 block w-full"
                      v-model="dates.end_time"
                      autofocus
                  />
                  <jet-input-error
                      :message="createBookingRequestForm.error('reservations.'+index+'.start_time')"
                      class="mt-2"
                  />
              </div>
              <jet-secondary-button v-if="numDates > 1" @click.native="removeDate(index)">
                Remove this date
              </jet-secondary-button>
            </div>
          <div class="m-6">
          <jet-secondary-button @click.native="addDate">
            Add Another date
          </jet-secondary-button>
          </div>

            <div class="m-6">
                <jet-input-error
                    :message="createBookingRequestForm.error('availabilities')"
                    class="mt-2"
                />
            </div>
            <div class="m-6">
                <jet-input-error
                    :message="createBookingRequestForm.error('booking_request_exceeded')"
                    class="mt-2"
                />
            </div>
          </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="closeModal">
                Nevermind
            </jet-secondary-button>

            <jet-button
                class="ml-2"
                @click.native="createBookingRequest"
                :class="{ 'opacity-25': createBookingRequestForm.processing }"
                :disabled="createBookingRequestForm.processing"
            >
                Create
            </jet-button>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetButton from "@src/Jetstream/Button";
import JetInput from "@src/Jetstream/Input";
import JetActionMessage from "@src/Jetstream/ActionMessage";
import JetFormSection from "@src/Jetstream/FormSection";
import JetInputError from "@src/Jetstream/InputError";
import JetLabel from "@src/Jetstream/Label";
import JetDialogModal from "@src/Jetstream/DialogModal";
import JetDropdown from "@src/Jetstream/Dropdown";
import JetDropdownLink from "@src/Jetstream/DropdownLink";
import JetNavLink from "@src/Components/Navbar/NavLink";
import JetSecondaryButton from "@src/Jetstream/SecondaryButton";
import DialogModal from "@src/Jetstream/DialogModal";
import Availabilities from "@src/Components/Availabilities";

export default {
  components: {
    DialogModal,
    JetButton,
    JetInput,
    JetFormSection,
    JetActionMessage,
    JetInputError,
    JetLabel,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetDialogModal,
    JetSecondaryButton,
    Availabilities
  },

  props: {
    room: {
      type: Object,
      required: false
    }
  },
  computed: {
    numDates: function() {
      return this.createBookingRequestForm.reservations.length;
    },
  },
  data() {
    return {
      createBookingRequestForm: this.$inertia.form(
        {
          room_id: null,
          reservations:[
            {
              start_time: "",
              end_time: "",
            }
          ],
          reference: []
        },
        {
          bag: "createReservationsRequest",
          resetOnSuccess: true
        }
      ),
      room_name: ""
    };
  },
  methods: {
    closeModal() {
        this.createBookingRequestForm.room_id = null;
        this.createBookingRequestForm.reservations = [
          {
            start_time: "",
            end_time: "",
          }
        ];
        this.createBookingRequestForm.reference = [];
        this.$emit("close");
    },
    addDate() {
      this.createBookingRequestForm.reservations.push({
        start_time: "",
        end_time: "",
      })
    },
    removeDate(pos) {
      this.createBookingRequestForm.reservations.splice(pos,1)
    },
    createBookingRequest() {
      this.createBookingRequestForm.post("/bookings/create", {
        preserveScroll: true
      }).then(() => {
        if (! this.createBookingRequestForm.hasErrors()) {
          this.closeModal();
        }
      });
    },
    fieldChange(e) {
      let selectedFiles = e.target.files;

      if (!selectedFiles.length) return false;

      for (let file of selectedFiles) {
        this.createBookingRequestForm.reference.push(file);
      }
    }
  },
  watch: {
        room(room) {
            this.createBookingRequestForm.room_id = room?.id;
            this.room_name = room?.name;
        }
    }
};
</script>
