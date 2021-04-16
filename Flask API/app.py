from flask import Flask, request
from flask_cors import CORS
from langdetect import detect
import spacy
import joblib
import re
import string

app = Flask(__name__)
CORS(app)


def get_model_victorizer():
    global svm, vect, nlp
    with open('model.pkl', 'rb') as f:
        svm = joblib.load(f)
    with open('tfIdf_vectorizer.pkl', 'rb') as f:
        vect = joblib.load(f)
    nlp = spacy.load('fr_core_news_md')

print("loading model and vectorizer ....")
get_model_victorizer()
print("loading finished !!!!")


@app.route("/hello", methods=["GET"])
def hello():
    return "hello word"

@app.route("/predict", methods=["Post"])
def predict():
    email= request.form["subject"]+" "+request.form["body"]
    # lower case subject and body columns
    email = email.lower()
    # remove unwanted patterns in forwarded messages
    email = re.sub('from:.*\nsent:.*\nto:.*\n(.*\n)*subject:.*', '', email)
    email = re.sub('from:.*\ndate:.*\nsubject:.*\n(.*\n)*to:.*\n', '', email)
    email = re.sub('de\s?:.*\nenvoyé\s?:.*\nà\s?:.*\n(.*\n)*objet\s?:.*', '', email)
    email = re.sub('caution: this email originat.+content is safe.', '', email)
    # remove: links and emails
    email = re.sub('http\S+', ' ', email)
    email = re.sub('www\.\S+\.\S+', ' ', email)
    email = re.sub('\S+@\S+', ' ', email)
    # Expand Abbreviation
    # replace tp, tp1, tp2, tp3 ===> travaux pratiques
    email = re.sub('tp[0-9]*', 'travaux pratiques', email)
    # replace tds, td1, td2 ===> travaux dirigés
    email = re.sub('td[0-9]+', 'travaux dirigés', email)
    # replace s1, s2, s3 ===> semestre
    email = re.sub('s[0-9]+', 'semestre', email)
    # replace um6p ===> université polytechnique
    email = re.sub('um[0-9]+p', 'université polytechnique', email)
    # remove numbers
    email = re.sub('\d+', '', email)
    # remove empty lines
    email = "".join([s for s in email.strip().splitlines(True) if s.strip()])
    # remove punctuation
    email = re.sub('[{}]'.format(string.punctuation), ' ', email)
    # remove leading and ending spaces
    email = email.strip()
    # remove duplicated spaces
    email = " ".join(email.split())

    # detecter la langue
    try:
        language = detect(email)
    except:
        language = "fr"
    if(language != "fr"):
        return "autre"
    # removing stop words and lemmitization
    email = " ".join([w.lemma_ for w in nlp(email) if not w.is_stop])
    # prediction
    v = vect.transform([email])
    s = svm.predict(v)
    return s[0]

if __name__ == "__main__":
    app.run(host="0.0.0.0", port="5000")