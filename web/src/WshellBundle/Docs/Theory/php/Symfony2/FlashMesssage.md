 $this->get('session')->getFlashBag()->add(
            'notice',
            'Your changes were saved!'
        );



{% for flashMessage in app.session.flashbag.get('notice') %}
    <div class="flash-notice">
        {{ flashMessage }}
    </div>
{% endfor %}
