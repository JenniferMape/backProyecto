<?php

class Subscription {
    
    public function getAllSubscriptions() {
        $subscriptions = ORM::for_table('subscriptions')->find_many();
        return $this->convertCollection($subscriptions);
    }

    public function getSubscriptionsByUser($id_user) {
        $subscriptions = ORM::for_table('subscriptions')->where('id_user_subscription', $id_user)->find_many();
        return $this->convertCollection($subscriptions);
    }

    public function addSubscription($data) {
        // Verificación básica de los datos antes de crear la suscripción
        if (empty($data['id_user_subscription']) || empty($data['id_category_subscription'])) {
            return false; // Retorna false si los datos no son válidos
        }

        $subscription = ORM::for_table('subscriptions')->create();
        $subscription->id_user_subscription = $data['id_user_subscription'];
        $subscription->id_category_subscription = $data['id_category_subscription'];
        $subscription->save();

        return $subscription->id(); // Retorna el ID de la nueva suscripción
    }

    public function updateSubscription($data) {
        $subscription = ORM::for_table('subscriptions')->find_one($data['id']);
    
        if ($subscription) {
            // Actualizar los datos de la suscripción en la base de datos
            $subscription->id_user_subscription = $data['id_user_subscription'] ?? $subscription->id_user_subscription;
            $subscription->id_category_subscription = $data['id_category_subscription'] ?? $subscription->id_category_subscription;

            // Guardar los cambios en la base de datos
            $subscription->save();

            return true;
        } else {
            return false;
        }
    }

    public function deleteSubscription($id) {
        $subscription = ORM::for_table('subscriptions')->find_one($id);
        if ($subscription) {
            $subscription->delete();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj) {
        return [
            'id' => $obj->id ?? null,
            'id_user_subscription' => $obj->id_user_subscription ?? null,
            'id_category_subscription' => $obj->id_category_subscription ?? null,
            'created_subscription' => $obj->created_subscription ?? null,
            'updated_subscription' => $obj->updated_subscription ?? null
        ];
    }
    private function convertCollection($collection)
    {
        $result = [];
        foreach ($collection as $item) {
            $result[] = $this->convertObj($item);
        }
        return $result;
    }

}
?>
